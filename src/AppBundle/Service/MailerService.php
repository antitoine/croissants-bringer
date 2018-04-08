<?php

namespace AppBundle\Service;

use AppBundle\Entity\Participation;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class MailerService
 * @package AppBundle\Service
 */
class MailerService implements MailerInterface
{
    /** @var \Swift_Mailer */
    protected $mailer;
    /** @var  EntityManager */
    protected $em;
    /** @var  \Twig_Environment */
    protected $template;
    /** @var array */
    protected $parameters;
    /** @var UrlGeneratorInterface */
    protected $router;
    /** @var Translator */
    protected $translator;

    /**
     * MailerService constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param EntityManager $em
     * @param \Twig_Environment $template
     * @param UrlGeneratorInterface $router
     * @param $translator
     * @param array $parameters
     */
    public function __construct(\Swift_Mailer $mailer, EntityManager $em, \Twig_Environment $template, UrlGeneratorInterface $router, Translator $translator, $parameters)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->template = $template;
        $this->router = $router;
        $this->translator = $translator;
        $this->parameters = $parameters;
    }

    /**
     * Send an email to the participant to get his approval
     *
     * @param Participation $participation
     * @return bool true if succeed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendParticipantApproval(Participation $participation)
    {
        $template = $this->template->render('email/participant_approval.html.twig', [  // TODO create the template
            'participation' => $participation,
        ]);

        return $this->sendMessage(
            $template,
            '[Croissants Bringer] - Demande de participation pour le ' . $participation->getDate()->format('d-m-Y'),
            $this->parameters['from'],
            $participation->getUser()->getEmail()
        );
    }

    /**
     * Send an email to a user to confirm the account creation
     *
     * @param UserInterface $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL);
        $template = $this->template->render('email/account_confirmation.html.twig',
            [
                'confirmationUrl' => $url,
                'user' => $user,
            ]);
        $this->sendMessage(
            $template,
            $this->translator->trans('email.subject.account_confirmation', null, 'croissants'),
            $this->parameters['from'],
            $user->getEmail()
        );
    }
    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL);
        $template = $this->template->render('email/reset_password.html.twig',
            [
                'confirmationUrl' => $url,
                'user' => $user,
            ]);
        $this->sendMessage(
            $template,
            $this->translator->trans('email.subject.reset_password', null, 'croissants'),
            $this->parameters['from'],
            $user->getEmail()
        );
    }

    /**
     * Send mail
     *
     * @param string $body
     * @param string $subject
     * @param string|array $fromEmail
     * @param string|array $to
     * @param array $cc
     * @param array $bcc
     *
     * @return bool true if sent, false if failed
     */
    private function sendMessage($body, $subject, $fromEmail, $to, $cc = [], $bcc = [])
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail);
        if (is_array($to)) {
            foreach ($to as $email) {
                $message->setTo($email);
            }
        } else {
            $message->setTo($to);
        }
        foreach ($cc as $email) {
            $message->setCC($email);
        }
        foreach ($bcc as $email) {
            $message->setBcc($email);
        }
        $message->setBody($body,'text/html');

        return 0 !== $this->mailer->send($message);
    }
}