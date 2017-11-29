<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
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
        /** @var UserRepository $userRepository */
        $userRepository = $this
            ->getContainer()
            ->get('doctrine')
            ->getRepository('AppBundle:User');

        /** @var User $user */
        $user = $userRepository->findCroissantsBringer();

        $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Bringer: ' . $user->getUsername());
    }
}