<?php

declare(strict_types=1);

use App\Command\RemoveUnlinkedEducationsCommand;
use Robo\Tasks;

class RoboFile extends Tasks
{
    public function resetdb(string $env = 'dev'): void
    {
        $this->dropAndRecreateDatabase($env);

        $this->yell('all done!');
    }

    public function runProject(): void
    {
        $this->taskExec('docker-compose up -d')->run();
        $this->taskExec('symfony composer install')->run();
        $this->taskExec('symfony proxy:start')->run();
        $this->taskExec('symfony proxy:domain:attach naarschoolinvlaanderen.be')->run();
        $this->taskExec('symfony serve -d')->run();

        $this->yell('all done!');
    }

    public function dropAndRecreateDatabase(string $env = 'dev'): void
    {
        $this->runCommand('doctrine:database:drop --force', $env);
        $this->runCommand('doctrine:database:create --if-not-exists', $env);
        $this->runCommand('doctrine:schema:create', $env);
        $this->runCommand('doctrine:fixtures:load -n', $env);
//        $this->runCommand(RemoveUnlinkedEducationsCommand::$defaultName, $env);
    }

    private function runCommand(string $command, string $env = 'dev'): void
    {
        $this->taskExec('symfony php bin/console '.$command)->arg('--env='.$env)->arg('-v')->run();
    }
}
