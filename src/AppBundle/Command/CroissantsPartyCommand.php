<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CroissantsPartyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:croissants-party')
            ->setDescription('Select a new croissants bringer.')
            ->setHelp('This command allows you to select a new croissants bringer and update the participation history.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $userManager = $this->getContainer()->get('fos_user.user_manager');
        // TODO get repository and select the new croissants bringer

        $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Bringer: ' );
    }
}