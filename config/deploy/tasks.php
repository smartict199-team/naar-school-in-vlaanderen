<?php

declare(strict_types=1);

use function Deployer\get;
use function Deployer\set;
use function Deployer\run;
use function Deployer\task;

set('bin/php', static function () {
    return 'php -d memory_limit=-1';
});

task('combell:reload_php', function (): void {
    run('reloadPHP.sh');
})->desc('Reload PHP-FPM');

task('combell:rsync_public_to_www', static function () {
    run('rsync -rtvul --delete {{ release_path }}/public/. {{ deploy_path }}/../www ');
})->desc('Copy web folder to www root');

task('combell:copy_index', function (): void {
    run('mv -f {{ release_path }}/public/{{ index_file }} {{ release_path }}/public/index.php');
});

task('combell:basic_auth', function(): void {
    $escapedReleasePath = preg_quote(get('release_path'), '/');
    run('sed \'s/__RELEASE_PATH__/' . $escapedReleasePath . '/g\' {{ release_path }}/etc/combell/staging/.htaccess > {{ release_path }}/public/.htaccess');
});

task('deploy:assets:install', function(): void {
    run("cd {{release_or_current_path}} && {{bin/console}} assets:install {{console_options}}");
});

task('combell:install_crontab', static function () {
    if (0 === (int) run('[ -f {{ release_path }}/{{ crontab_repo_path }} ] && echo "1" || echo "0"')) {
        return;
    }

    $uniqueMarker = sha1(get('deploy_path'));
    $startMarker = sprintf('#### START %s ####', $uniqueMarker);
    $endMarker = sprintf('#### END %s ####', $uniqueMarker);
    $count = (int) run(sprintf('grep -c \'%s\' {{ deploy_path }}/{{ crontab_remote_path }} || :', $startMarker));

    if ($count > 0) {
        run(sprintf("sed -i '/%s/,/%s/d' {{ deploy_path }}/{{ crontab_remote_path }}", $startMarker, $endMarker));
    }

    run(sprintf("echo '%s' >> {{ deploy_path }}/{{ crontab_remote_path }}", $startMarker));
    run(sprintf('cat {{ release_path }}/{{ crontab_repo_path }}  >> {{ deploy_path }}/{{ crontab_remote_path }}'));
    run(sprintf("echo '%s' >> {{ deploy_path }}/{{ crontab_remote_path }}", $endMarker));
})->desc('Update crontab');

