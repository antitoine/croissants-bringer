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
     * @throws AccessDeniedException
     */
    protected function checkApproval(Participation $participation)
    {
        if (!$participation->needApprovalFromParticipant()) {
            throw $this->createAccessDeniedException('The participation doesn\'t require an approval.');
        }

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
        $this->checkApproval($participation);

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
        $this->checkApproval($participation);

        $participation->setStatus(ParticipationStatusEnum::STATUS_REFUSED);
        $this->getDoctrine()->getManager()->flush();

        // TODO

        return $this->redirectToRoute('history');
    }

    /**
     * @param Participation $participation
     *
     * @throws AccessDeniedException
     */
    protected function checkAccomplish(Participation $participation)
    {
        if (!$participation->needAccomplishConfirmation()) {
            throw $this->createAccessDeniedException('The participation doesn\'t require a confirmation.');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();

        if ($participation->getUser()->getId() === $user->getId()) {
            throw $this->createAccessDeniedException('Your are the participant of this participation, you can\'t confirm the accomplishment.');
        }
    }

    /**
     * @Route("/accomplish", name="participation_accomplish")
     *
     * @param Participation $participation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accomplishParticipationPageAction(Participation $participation)
    {
        $this->checkAccomplish($participation);

        $participation->setStatus(ParticipationStatusEnum::STATUS_DONE);
        $participation->setConfirmedBy($this->getUser());
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
        $this->checkAccomplish($participation);

        $participation->setStatus(ParticipationStatusEnum::STATUS_FAILED);
        $participation->setConfirmedBy($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('history');
    }
}
