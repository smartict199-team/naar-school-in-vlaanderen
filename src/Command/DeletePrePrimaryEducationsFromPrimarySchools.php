<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class DeletePrePrimaryEducationsFromPrimarySchools extends Command
{
    public static $defaultName = 'app:delete-pre-primary-educations-from-primary-schools';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Toon welke rijen verwijderd zouden worden, zonder effectief te verwijderen'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = $this->em->getConnection();

        $qb = $conn->createQueryBuilder();

        $qb->select('se.id')
            ->from('app_school_educations', 'se')
            ->innerJoin('se', 'app_schools', 's', 'se.school_id = s.id')
            ->where('s.type = :schoolType')
            ->andWhere('se.level_id = :levelId')
            ->setParameter('schoolType', 'only_primary_education')
            ->setParameter('levelId', 12);

        $rows = $qb->execute()->fetchFirstColumn();


        if (count($rows) === 0) {
            $output->writeln('<comment>Geen rijen gevonden.</comment>');
            return Command::SUCCESS;
        }

        if ($input->getOption('dry-run')) {
            $output->writeln('<info>Dry-run modus geactiveerd. De volgende IDs zouden verwijderd worden:</info>');
            foreach ($rows as $id) {
                $output->writeln(" - ID: $id");
            }
            $output->writeln("<comment>Totaal: " . count($rows) . " rijen zouden verwijderd worden.</comment>");
            return Command::SUCCESS;
        }

        $deleted = $conn->executeStatement(
            'DELETE FROM app_school_educations WHERE id IN (:ids)',
            ['ids' => $rows],
            ['ids' => Connection::PARAM_INT_ARRAY]
        );

        $output->writeln("<info>$deleted rijen verwijderd uit app_school_educations.</info>");
        return Command::SUCCESS;
    }
}
