<?php

namespace AppBundle\Command;

use AppBundle\Entity\Participation;
use AppBundle\Entity\User;
use AppBundle\Enum\ParticipationStatusEnum;
use AppBundle\Repository\ParticipationRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
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

    /**
     * This command line allow to select a new participant if the last one has done his mission,
     * or send a notification to confirm if the mission is done,
     * or send a notification to get the approval of the participant if no given yet
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository('AppBundle:User');
        $this->participationRepository = $this->entityManager->getRepository('AppBundle:Participation');

        /** @var Participation $lastParticipation */
        $lastParticipation = $this->participationRepository->findLastParticipation();

        if (!is_null($lastParticipation)) {

            // If wa have the confirmation that the mission is done and we are not a friday day
            // a new croissant bringer need to be chosen for the new week
            if ($lastParticipation->getStatus() === ParticipationStatusEnum::STATUS_DONE && date('w') !== 5) {

                /** @var Participation $newParticipation */
                $newParticipation = $this->makeNewParticipation();

                if (!is_null($newParticipation)) {
                    $this->sendRequestToBringer($newParticipation->getUser());
                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Send request to new bringer to get his approval: ' . $newParticipation->getUser()->getUsername());
                } else {
                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - No Bringer available for now');
                }

            // If we haven't confirmation that the mission is done and the mission date is passed, ask for confirmation
            } else if ($lastParticipation->getStatus() === ParticipationStatusEnum::STATUS_PENDING && $lastParticipation->getDate() < new \DateTime()) {

                $this->sendRequestToGetConfirmation();

                // This code allow to switch the participant from pending to done and increment users positions.
                // This can't be done in the command line, but when a user confirm that the mission is done.
                //
                //$lastParticipation->setStatus(ParticipationStatusEnum::STATUS_DONE);
                //$this->userRepository->incrementUsersPosition();
                //$lastParticipation->getUser()->setPosition(0);
                //$this->entityManager->flush();

            // If we are still waiting participation response
            } else if ($lastParticipation->getStatus() === ParticipationStatusEnum::STATUS_WAITING) {

                // Check if the user is still a participant
                if ($lastParticipation->getUser()->isParticipant()) {
                    $this->sendRequestToBringer($lastParticipation->getUser());
                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Send request again to bringer to get his approval: ' . $lastParticipation->getUser()->getUsername());
                } else {

                    $output->writeln('[' . date('Y-m-d H:i:s') . '] Croissants Party - Previous bringer ("' . $lastParticipation->getUser()->getUsername() . '") is not a participant any more, will find a new one');

                    /** @var User $user */
                    $user = $this->userRepository->findCroissantsBringer();

                    if (!is_null($user)) {
                        $lastParticipation->setUser($user);

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
     * @throws OptimisticLockException
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
        $newParticipation->setDate(self::getNextFriday());
        $newParticipation->setUser($user);

        $this->entityManager->persist($newParticipation);
        $this->entityManager->flush();

        return $newParticipation;
    }

    /**
     * @return \DateTime
     */
    static protected function getNextFriday()
    {
        $date = new \DateTime();
        $date->modify('next friday');
        return $date;
    }

    protected function sendRequestToBringer(User $user)
    {
        // TODO
    }

    protected function sendRequestToGetConfirmation()
    {
        // TODO
    }
}