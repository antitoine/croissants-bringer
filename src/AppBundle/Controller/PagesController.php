<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Participation;
use AppBundle\Entity\User;
use AppBundle\Enum\ParticipationStatusEnum;
use AppBundle\Enum\UserPreferenceEnum;
use AppBundle\Enum\UserStatusEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PagesController
 */
class PagesController extends Controller
{
    /**
     * @Route("/", name="home")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homePageAction()
    {
        return $this->render('page/home.html.twig', [
            'alertList' => $this->getAlerts(),
        ]);
    }

    /**
     * @Route("/participant", name="participant")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function participantsPageAction()
    {
        $userRepository = $this->get('doctrine')->getRepository('AppBundle:User');

        $participants = $userRepository->findBy([
            'participant' => true,
            'enabled' => true,
            'status' => UserStatusEnum::STATUS_EMPLOYED,
        ], [
            'position' => 'DESC',
        ]);

        $participantsOnlyConsumer = $userRepository->findBy([
            'participant' => true,
            'enabled' => true,
            'status' => [
                UserStatusEnum::STATUS_TRAINEE,
                UserStatusEnum::STATUS_TRIAL,
            ],
        ], [
            'position' => 'DESC',
        ]);

        $nonParticipants = $userRepository->findBy([
            'participant' => false,
            'enabled' => true,
        ], [
            'position' => 'DESC',
        ]);

        $nbCroissants = 0;
        $nbPainsAuChocolat = 0;
        /** @var User $participant */
        foreach ($participants as $participant) {
            $nbCroissants += UserPreferenceEnum::getNbCroissants($participant->getPreference());
            $nbPainsAuChocolat += UserPreferenceEnum::getNbPainAuChocolat($participant->getPreference());
        }
        /** @var User $participant */
        foreach ($participantsOnlyConsumer as $participant) {
            $nbCroissants += UserPreferenceEnum::getNbCroissants($participant->getPreference());
            $nbPainsAuChocolat += UserPreferenceEnum::getNbPainAuChocolat($participant->getPreference());
        }

        return $this->render('page/participants.html.twig', [
            'participants' => $participants,
            'nonParticipants' => $nonParticipants,
            'participantsOnlyConsumer' => $participantsOnlyConsumer,
            'nbCroissants' => $nbCroissants,
            'nbPainsAuChocolat' => $nbPainsAuChocolat,
            'alertList' => $this->getAlerts(),
        ]);
    }

    /**
     * @Route("/history", name="history")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyPageAction()
    {
        $participationRepository = $this->get('doctrine')->getRepository('AppBundle:Participation');

        $participationList = $participationRepository->findBy([], ['date' => 'DESC', 'id' => 'DESC']);

        return $this->render('page/history.html.twig', [
            'participationList' => $participationList,
            'alertList' => $this->getAlerts(),
        ]);
    }

    /**
     * @return array
     */
    protected function getAlerts()
    {
        $alertList = [];

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $alertList;
        }

        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $alertList;
        }

        $participationRepository = $this->get('doctrine')->getRepository('AppBundle:Participation');

        /** @var Participation $lastParticipation */
        $lastParticipation = $participationRepository->findLastParticipation();
        if (!$lastParticipation) {
            return $alertList;
        }

        if ($lastParticipation->needAccomplishConfirmation() && $lastParticipation->getUser()->getId() !== $user->getId()) {
            $alertList[] = [
                'type' => 'ACCOMPLISH_CONFIRMATION',
                'participation' => $lastParticipation,
            ];
        }

        if ($lastParticipation->needApprovalFromParticipant() && $lastParticipation->getUser()->getId() === $user->getId()) {
            $alertList[] = [
                'type' => 'PARTICIPANT_APPROVAL',
                'participation' => $lastParticipation,
            ];
        }

        return $alertList;
    }
}
