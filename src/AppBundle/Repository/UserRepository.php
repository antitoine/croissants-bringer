<?php

namespace AppBundle\Repository;

/**
 * UserRepository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findCroissantsBringer()
    {
        $query = $this
            ->createQueryBuilder('u')
            ->where('u.position = (SELECT MIN(u2.position) FROM AppBundle:User u2)');
        return $query->getQuery()->getOneOrNullResult();
    }

    public function updateCroissantsBringer(User $bringer)
    {
        // TODO
    }

    public function decrementUsersPosition()
    {
        // TODO
    }
}
