<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\SchoolEducation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveUnlinkedEducationsCommand extends Command
{
    public static $defaultName = 'app:remove:unlinked:educations';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct(self::$defaultName);
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Command to remove educations not linked to a year, this is required after running fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $this->entityManager->createQuery('DELETE FROM ' . SchoolEducation::class . ' se WHERE se.year IS NULL');
        $query->execute();

        return Command::SUCCESS;
    }
}
