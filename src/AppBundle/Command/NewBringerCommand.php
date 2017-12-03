<?php

namespace AppBundle\Command;

use AppBundle\Entity\Participation;
use AppBundle\Entity\User;
use AppBundle\Enum\ParticipationStatusEnum;
use AppBundle\Repository\ParticipationRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewBringerCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository;

    /**
     * @var ParticipationRepository $participationRepository
     */
    protected $participationRepository;

    protected function configure()
    {
        $this
            ->setName('app:new-bringer')
            ->setDescription('Select a new croissants bringer.')
            ->setHelp('This command allows you to select a new croissants bringer and update the participation history.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository('AppBundle:User');
        $this->participationRepository = $this->entityManager->getRepository('AppBundle:Participation');

        /** @var Participation $lastParticipation */
        $lastParticipation = $this->participationRepository->findLastParticipation();

        if (!is_null($lastParticipation)) {

            if ($lastParticipation->getStatus() === ParticipationStatusEnum::STATUS_VALIDATED) {

                $lastParticipation->setStatus(ParticipationStatusEnum::STATUS_DONE);
                $this->userRepository->incrementUsersPosition();
                $lastParticipation->getUser()->setPosition(0);

                $this->entityManager->flush();

                /** @var Participation $newParticipation */
                $newParticipation = $this->makeNewParticipation();

                if (!is_null($newParticipation)) {
                    $this->sendRequestToBringer($newParticipation->getUser());
                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Send request to new bringer: ' . $newParticipation->getUser()->getUsername());
                } else {
                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - No Bringer available for now');
                }

            } else if ($lastParticipation->getStatus() === ParticipationStatusEnum::STATUS_PENDING) {

                // Check if the user is still a participant
                if ($lastParticipation->getUser()->isParticipant()) {
                    $this->sendRequestToBringer($lastParticipation->getUser());
                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Send request to pending bringer: ' . $lastParticipation->getUser()->getUsername());
                } else {
                    /** @var User $user */
                    $user = $this->userRepository->findCroissantsBringer();

                    if (!is_null($user)) {
                        $lastParticipation->setUser($user);
                        $lastParticipation->setDate(new \DateTime());

                        $this->entityManager->flush();

                        $this->sendRequestToBringer($lastParticipation->getUser());

                        $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Send request to new bringer: ' . $lastParticipation->getUser()->getUsername());
                    } else {
                        $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - No Bringer available for now');
                    }
                }
            }
        }
    }

    /**
     * @return Participation|null
     */
    protected function makeNewParticipation()
    {
        /** @var User $user */
        $user = $this->userRepository->findCroissantsBringer();

        if (is_null($user)) {
            return null;
        }

        $newParticipation = new Participation();
        $newParticipation->setStatus(ParticipationStatusEnum::STATUS_PENDING);
        $newParticipation->setDate(new \DateTime());
        $newParticipation->setUser($user);

        $this->entityManager->persist($newParticipation);
        $this->entityManager->flush();

        return $newParticipation;
    }

    protected function sendRequestToBringer(User $user)
    {
        // TODO
    }
}