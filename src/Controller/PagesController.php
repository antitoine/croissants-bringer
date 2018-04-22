<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\User;
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
        $userRepository = $this->get('doctrine')->getRepository('App:User');

        $participants = $userRepository->findBy([
            'participant' => true,
        ], [
            'position' => 'DESC',
        ]);

        return $this->render('page/participants.html.twig', [
            'participants' => $participants,
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
        $participationRepository = $this->get('doctrine')->getRepository('App:Participation');

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

        $participationRepository = $this->get('doctrine')->getRepository('App:Participation');

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