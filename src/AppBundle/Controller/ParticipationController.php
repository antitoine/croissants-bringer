<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Participation;
use AppBundle\Entity\User;
use AppBundle\Enum\ParticipationStatusEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @param Participation $participation
     *
     * @throws AccessDeniedException if not the participant of the participation
     */
    protected function checkIsParticipant(Participation $participation)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();

        if ($participation->getUser()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Your are not the participant of this participation, you can\'t accept or refuse it.');
        }
    }

    /**
     * @Route("/accept", name="participation_accept")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptParticipationPageAction(Participation $participation)
    {
        $this->checkIsParticipant($participation);

        $participation->setStatus(ParticipationStatusEnum::STATUS_PENDING);
        $this->getDoctrine()->getManager()->flush();

        // TODO send notification to confirm ?

        return $this->redirectToRoute('history');
    }

    /**
     * @Route("/refuse", name="participation_refuse")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refuseParticipationPageAction(Participation $participation)
    {
        $this->checkIsParticipant($participation);

        // TODO

        return $this->redirectToRoute('history');
    }

    /**
     * @Route("/accomplish", name="participation_accomplish")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accomplishParticipationPageAction(Participation $participation)
    {
        $participation->setStatus(ParticipationStatusEnum::STATUS_DONE);
        $this->getDoctrine()->getRepository('AppBundle:User')->incrementUsersPosition();
        $participation->getUser()->setPosition(0);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('history');
    }

    /**
     * @Route("/fail", name="participation_fail")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function failParticipationPageAction(Participation $participation)
    {
        $participation->setStatus(ParticipationStatusEnum::STATUS_FAILED);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('history');
    }
}
