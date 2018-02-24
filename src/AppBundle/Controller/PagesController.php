<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PagesController
 */
class PagesController extends Controller
{
    /**
     * @Route("/", name="home")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homePageAction(Request $request)
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/participant", name="participant")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function participantsPageAction(Request $request)
    {
        $userRepository = $this->get('doctrine')->getRepository('AppBundle:User');

        $participants = $userRepository->findBy([
            'participant' => true,
        ], [
            'position' => 'DESC',
        ]);

        return $this->render('page/participants.html.twig', [
            'participants' => $participants,
        ]);
    }

    /**
     * @Route("/history", name="history")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyPageAction(Request $request)
    {
        $participationRepository = $this->get('doctrine')->getRepository('AppBundle:Participation');

        $participationList = $participationRepository->findBy([], ['date' => 'DESC']);

        return $this->render('page/history.html.twig', [
            'participationList' => $participationList,
        ]);
    }
}
