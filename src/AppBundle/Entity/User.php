<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
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
     * @var array
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Participation", mappedBy="user")
     */
    protected $participationList;

    /**
     * Position 0 => croissants bringer of the week
     * Position >0 => next croissants bringer for others weeks
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getParticipationList()
    {
        return $this->participationList;
    }

    /**
     * @param array $participationList
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
}