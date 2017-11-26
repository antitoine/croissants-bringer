<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
     * @var array
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Participation", mappedBy="user")
     */
    protected $participationList;

    /**
     * @var integer
     *
     * @ORM\Column(name="rejected_count", type="integer")
     */
    protected $rejectedCount;

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
        /** @var Participation $participationMade */
        foreach($this->participationList as $participationMade) {
            if ($participation->getId() === $participationMade->getId()) {
                return;
            }
        }
        $this->participationList[] = $participation;
    }

    /**
     * @return int
     */
    public function getRejectedCount()
    {
        return $this->rejectedCount;
    }

    /**
     * @param int $rejectedCount
     */
    public function setRejectedCount($rejectedCount)
    {
        $this->rejectedCount = $rejectedCount;
    }
}