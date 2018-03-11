<?php

namespace AppBundle\Command;

use AppBundle\Service\BringerManagerService;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BringerCommand extends ContainerAwareCommand
{
    /**
     * @var BringerManagerService $bringerManager
     */
    protected $bringerManager;

    public function __construct(BringerManagerService $bringerManager)
    {
        $this->bringerManager = $bringerManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:bringer')
            ->setDescription('Manage croissants bringers.')
            ->setHelp('This command manage croissants bringers.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bringerManager->checkAndManage();
    }
}