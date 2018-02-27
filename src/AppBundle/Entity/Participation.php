<?php

namespace AppBundle\Entity;

use AppBundle\Enum\ParticipationStatusEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParticipationRepository")
 */
class Participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime the date of the mission (a friday day)
     *
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="participationList")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string match a status available in the {@see ParticipationStatusEnum} class
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=false)
     */
    protected $status;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="confirmed_by_id", referencedColumnName="id")
     */
    protected $confirmedBy;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Participation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     * @see ParticipationStatusEnum
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @see ParticipationStatusEnum
     */
    public function setStatus($status)
    {
        if (!in_array($status, ParticipationStatusEnum::getAvailableStatus())) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getConfirmedBy()
    {
        return $this->confirmedBy;
    }

    /**
     * @param User $confirmedBy
     */
    public function setConfirmedBy($confirmedBy)
    {
        $this->confirmedBy = $confirmedBy;
    }

    /**
     * @return bool true if the participation is in waiting status for approval from the participant
     */
    public function NeedApprovalFromParticipant()
    {
        return $this->getStatus() === ParticipationStatusEnum::STATUS_ASKING;
    }

    /**
     * @return bool true if the participation is in pending status and the due date for accomplish the mission is passed
     */
    public function NeedAccomplishConfirmation()
    {
        return $this->getStatus() === ParticipationStatusEnum::STATUS_PENDING && $this->getDate() < new \DateTime();
    }

    /**
     * @return bool true if the mission is done (status) and we passed the friday day (need a new participation for the new week)
     */
    public function NeedNewParticipation()
    {
        $status = $this->getStatus();
        return ($status === ParticipationStatusEnum::STATUS_DONE && date('w') !== 5)
            || $status === ParticipationStatusEnum::STATUS_FAILED
            || $status === ParticipationStatusEnum::STATUS_REFUSED;
    }
}

