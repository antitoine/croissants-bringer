<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Participation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ParticipationController
 *
 * @Route("/participation/{id}")
 * @ParamConverter("participation", class="AppBundle\Entity\Participation")
 */
class ParticipationController extends Controller
{
    /**
     * @Route("/", name="participation")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showParticipationAction(Participation $participation)
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/accept", name="participation_accept")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptParticipationPageAction(Participation $participation)
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/refuse", name="participation_refuse")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refuseParticipationPageAction(Participation $participation)
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/accomplish", name="participation_accomplish")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accomplishParticipationPageAction(Participation $participation)
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/fail", name="participation_fail")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function failParticipationPageAction(Participation $participation)
    {
        return $this->render('page/home.html.twig');
    }
}
