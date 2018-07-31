<?php

namespace AppBundle\Entity;

use AppBundle\Enum\ParticipationStatusEnum;
use AppBundle\Enum\UserPreferenceEnum;
use AppBundle\Enum\UserStatusEnum;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\Length(
     *     min=3,
     *     max=5,
     *     minMessage="user.password.short",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     */
    protected $username;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Participation", mappedBy="user")
     */
    protected $participationList;

    /**
     * More the position is high, more the user have chance to be the croissant bringer
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="participant", type="boolean")
     */
    protected $participant = true;

    /**
     * @var string match a status available in the {@see UserStatusEnum} class
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=false)
     */
    protected $status = UserStatusEnum::STATUS_EMPLOYED;

    /**
     * @var string match a preference available in the {@see UserPreferenceEnum} class
     *
     * @ORM\Column(name="preference", type="string", length=255, nullable=false)
     */
    protected $preference = UserPreferenceEnum::PREFERENCE_CROISSANT_PAIN_AU_CHOCOLAT;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipationList()
    {
        return $this->participationList;
    }

    /**
     * @param array|ArrayCollection $participationList
     */
    protected function setParticipationList($participationList)
    {
        $this->participationList = $participationList;
    }

    /**
     * @param Participation $participation
     */
    public function addParticipation(Participation $participation)
    {
        /** @var Participation $participationDone */
        foreach($this->participationList as $participationDone) {
            if ($participation->getId() === $participationDone->getId()) {
                return;
            }
        }
        $this->participationList[] = $participation;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition( $position ) {
        $this->position = $position;
    }

    /**
     * @return bool
     */
    public function isParticipant()
    {
        return $this->participant;
    }

    /**
     * @param bool $participant
     */
    public function setParticipant($participant)
    {
        $this->participant = $participant;
    }

    /**
     * @return string
     * @see UserStatusEnum
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @see UserStatusEnum
     */
    public function setStatus($status)
    {
        if (!in_array($status, UserStatusEnum::getShortNameList())) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;
    }

    /**
     * @return string
     * @see UserPreferenceEnum
     */
    public function getPreference()
    {
        return $this->preference;
    }

    /**
     * @param string $preference
     * @see UserPreferenceEnum
     */
    public function setPreference($preference)
    {
        if (!in_array($preference, UserPreferenceEnum::getShortNameList())) {
            throw new \InvalidArgumentException('Invalid preference');
        }

        $this->preference = $preference;
    }

    /**
     * @return array
     */
    public function getParticipationDoneList()
    {
        if ($this->participationList === null || count($this->participationList) <= 0) {
            return [];
        }
        return array_filter($this->participationList->toArray(), function (Participation $participation) {
            return $participation->getStatus() === ParticipationStatusEnum::STATUS_DONE;
        });
    }
}