<?php

namespace Deployer;

use function Deployer\Support\array_to_string;

set('validate_staging', true);
set('php_container_alias', 'php');
set('docker-compose', '/usr/local/bin/docker-compose');
set('docker', '/usr/bin/docker');
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-suggest');

set('bin/bash', static function () {
    return '/bin/bash';
});

$stage = 'staging';

task('deploy:validate_staging', static function () {
    if (true !== get('validate_staging')) {
        return null;
    }
    if (false === askConfirmation('You should deploy:staging for in-house staging deployments! Continue?')) {
        exit();
    }
})->onStage($stage);

task('deploy:staging:prepare', static function () {
    $releaseExists = test('[ -d {{deploy_path}} ]');

    if (false === $releaseExists) {
        run('{{ bin/git }} clone {{ repository }} {{ deploy_path }} 2>&1');
    }

    cd('{{ deploy_path }}');
    run('{{ docker-compose }} -f {{ docker_compose_file }} up -d');
})->desc('Prepare staging evironment and start containers')->onStage($stage);

task('deploy:staging:update_code', static function () {
    cd('{{ deploy_path }}');

    $branch = get('branch');
    $currentBranch = run('{{ bin/git }} rev-parse --abbrev-ref HEAD');

    if ($branch !== $currentBranch) {
        run('{{ bin/git }} fetch origin {{ branch }}:{{ branch }}');
    }

    run('{{ bin/git }} checkout origin/{{ branch }}');
    run('{{ bin/git }} pull origin {{ branch }}');
})->desc('Update code')->onStage($stage);

task('deploy:staging:pull_docker_images', static function () {
    cd('{{ deploy_path }}');
    run('{{ docker-compose }} -f {{ docker_compose_file }} pull');
})->desc('Pull docker images')->onStage($stage);

task('deploy:staging:start_docker_containers', static function () {
    cd('{{ deploy_path }}');
    run('{{ docker-compose }} -f {{ docker_compose_file }} up -d ');
})->desc('Start docker containers')->onStage($stage);

task('deploy:staging:vendors', static function () {
    set('disable_host_key_check', '
Host *
    StrictHostKeyChecking no
    UserKnownHostsFile=/dev/null/
');

    $env = get('env', []) + ($options['env'] ?? []);
    if (!empty($env)) {
        $env = array_to_string($env);
    }

    runInContainer(get('php_container_alias'), '{{ bin/bash }} -c "mkdir -p /root/.ssh && echo \'{{ disable_host_key_check }}\' > /root/.ssh/config"');
    runInContainer(get('php_container_alias'), sprintf('{{ bin/bash }} -c "export %s; composer {{ composer_options }}"', $env));
})->desc('Installing vendors')->onStage($stage);

task('deploy:staging:clear_cache', static function () {
    runInContainer(get('php_container_alias'), 'rm -rv var/cache');
    runInContainer(get('php_container_alias'), 'bin/console cache:clear --env=staging');
    runInContainer(get('php_container_alias'), 'bin/console cache:warmup --env=staging');
})->desc('Clear cache')->onStage($stage);

task('deploy:staging:migrations', static function () {
    runInContainer(get('php_container_alias'), 'bin/console doctrine:migrations:migrate --allow-no-migration -n -q --env=staging');
})->desc('Doctrine migrations')->onStage($stage);

task('deploy:staging:writable', static function () {
    $dirs = implode(' ', get('writable_dirs'));
    runInContainer(get('php_container_alias'), sprintf('mkdir -p %s', $dirs));
    runInContainer(get('php_container_alias'), sprintf('chmod -R {{ writable_chmod_mode }} %s', $dirs));
})->desc('Make dirs writable')->onStage($stage);

task('deploy:staging:reset', static function () {
    runInContainer(get('php_container_alias'), './vendor/bin/robo resetdb staging');
})->desc('Reset database and run fixtures')->onStage($stage);

task('deploy:staging', [
    'deploy:staging:update_code',
//    'deploy:staging:pull_docker_images',
    'deploy:staging:start_docker_containers',
    'deploy:staging:vendors',
    'deploy:staging:clear_cache',
    'deploy:staging:migrations',
    'deploy:staging:writable',
])->desc('Deploy project to docker staging');

before('deploy', 'deploy:validate_staging');
after('deploy:staging', 'success');

function runInContainer(string $containerAlias, string $cmd)
{
    cd('{{ deploy_path }}');
    $exec = sprintf('{{ docker }} exec `{{ docker-compose }} -f {{ docker_compose_file }} ps -q %s`', $containerAlias);
    run(sprintf('%s %s', $exec, $cmd));
}
