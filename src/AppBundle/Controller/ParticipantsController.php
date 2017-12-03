<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * @Route("/participants")
 */
class ParticipantsController extends Controller
{
    /**
     * @Route("/", name="participants")
     */
    public function indexAction(Request $request)
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
}
