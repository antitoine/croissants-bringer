<?php

namespace App\Service;

use App\Entity\Participation;
use App\Entity\User;
use App\Enum\ParticipationStatusEnum;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

/**
 * Class BringerManagerService
 * @package App\Service
 */
class BringerManagerService
{
    /** @var  EntityManager */
    protected $em;
    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository;
    /**
     * @var ParticipationRepository $participationRepository
     */
    protected $participationRepository;
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;
    /**
     * @var MailerService
     */
    protected $mailer;

    public function __construct(EntityManager $em, LoggerInterface $logger, MailerService $mailer)
    {
        $this->em = $em;
        $this->userRepository = $this->em->getRepository('App:User');
        $this->participationRepository = $this->em->getRepository('App:Participation');
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * This command line allow to select a new participant if the last one has done his mission,
     * or send a notification to confirm if the mission is done,
     * or send a notification to get the approval of the participant if no given yet
     *
     * @throws OptimisticLockException
     */
    public function checkAndManage()
    {
        /** @var Participation $lastParticipation */
        $lastParticipation = $this->participationRepository->findLastParticipation();

        if (!is_null($lastParticipation)) {

            if ($lastParticipation->needNewParticipation()) {

                /** @var Participation $newParticipation */
                $newParticipation = $this->getNewParticipation();

                if (!is_null($newParticipation)) {
                    $this->sendRequestToBringer($newParticipation);
                    $this->logger->info('Send request to new bringer to get his approval: ' . $newParticipation->getUser()->getUsername());
                } else {
                    $this->logger->info('No Bringer available for now');
                }

            } else if ($lastParticipation->needAccomplishConfirmation()) {

                $this->sendRequestToGetConfirmation();

            } else if ($lastParticipation->needApprovalFromParticipant()) {

                // Check if the user is still a participant
                if ($lastParticipation->getUser()->isParticipant()) {
                    $this->sendRequestToBringer($lastParticipation);
                    $this->logger->info('Send request again to bringer to get his approval: ' . $lastParticipation->getUser()->getUsername());
                } else {

                    $this->logger->info('Previous bringer ("' . $lastParticipation->getUser()->getUsername() . '") is not a participant any more, will find a new one');

                    /** @var User $user */
                    $user = $this->userRepository->findCroissantsBringer();

                    if (!is_null($user)) {
                        $lastParticipation->setUser($user);

                        $this->em->flush();

                        $this->sendRequestToBringer($lastParticipation->getUser());

                        $this->logger->info('Send request to new bringer: ' . $lastParticipation->getUser()->getUsername());
                    } else {
                        $this->logger->info('No Bringer available for now');
                    }
                }
            } else {
                $this->logger->info('Nothing to do for now');
            }

        } else {
            $newParticipation = $this->getNewParticipation();

            if (!is_null($newParticipation)) {
                $this->sendRequestToBringer($newParticipation);
                $this->logger->info('Send request to new bringer to get his approval: ' . $newParticipation->getUser()->getUsername());
            } else {
                $this->logger->info('No Bringer available for now');
            }
        }
    }

    /**
     * @return Participation|null
     * @throws OptimisticLockException
     */
    public function getNewParticipation()
    {
        $date = static::getNextFriday();

        $excludedUserIdList = $this->participationRepository->findExcludedUsers($date);

        /** @var User $user */
        $user = $this->userRepository->findCroissantsBringer($excludedUserIdList);

        if (is_null($user)) {
            return null;
        }

        $newParticipation = new Participation();
        $newParticipation->setStatus(ParticipationStatusEnum::STATUS_ASKING);
        $newParticipation->setDate($date);
        $newParticipation->setUser($user);

        $this->em->persist($newParticipation);
        $this->em->flush();

        return $newParticipation;
    }

    /**
     * @return \DateTime
     */
    static public function getNextFriday()
    {
        $date = new \DateTime();
        $date->modify('next friday');
        return $date;
    }

    protected function sendRequestToBringer(Participation $participation)
    {
        try {
            $this->mailer->sendParticipantApproval($participation);
        } catch (\Twig_Error_Loader $e) {
            $this->logger->critical('Unable to send the request email to the participant "' . $participation->getUser()->getUsername() . '": ' . $e->getMessage());
        } catch (\Twig_Error_Runtime $e) {
            $this->logger->critical('Unable to send the request email to the participant "' . $participation->getUser()->getUsername() . '": ' . $e->getMessage());
        } catch (\Twig_Error_Syntax $e) {
            $this->logger->critical('Unable to send the request email to the participant "' . $participation->getUser()->getUsername() . '": ' . $e->getMessage());
        }
    }

    protected function sendRequestToGetConfirmation()
    {
        // TODO
    }
}