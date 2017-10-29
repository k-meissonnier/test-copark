<?php

namespace AppBundle\Command;

use AppBundle\Service\CoparkService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeParkingCommand extends ContainerAwareCommand
{
    protected $coparkService;

    protected function configure()
    {
        $this
            ->setName('app:parking:synchronize')
            ->setDescription('Synchronize parking from Copark API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->coparkService = $this->getContainer()->get(CoparkService::class);

        // Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : '.$now->format('d-m-Y G:i:s').' ---</comment>');

        // Importing parking from Copark API
        $this->coparkService->synchronizeParkingFromApi();

        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : '.$now->format('d-m-Y G:i:s').' ---</comment>');
    }
}
