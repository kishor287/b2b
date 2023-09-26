<?php

namespace server\Commands;

require_once('../../vendor/autoload.php');
require_once('../Classes/Seeder/SimInventorySeeder.php');

use server\Classes\Seeder\SimInventorySeeder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SimInventorySeederCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('seed:sim-inventory')
            ->setDescription('Seed the sim inventory')
            ->setHelp('This command seeds the sim inventory data.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Sim Inventory Seeder',
            '====================',
            '',
        ]);

        try {
            $simSeeder = new SimInventorySeeder('cp_sim_inventory');
            $simSeeder->makeData(30);
            $output->writeln('Seeded');
        } catch (\Throwable $th) {
            $output->writeln('Error: ' . $th->getMessage());
        }

        return Command::SUCCESS;
    }
}
