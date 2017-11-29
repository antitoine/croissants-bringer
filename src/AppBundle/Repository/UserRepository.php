<?php

namespace AppBundle\Repository;

use FOS\UserBundle\Doctrine\UserManager;

/**
 * UserRepository
 */
class UserRepository extends UserManager
{
    public function findCroissantsBringer()
    {
        return $this
            ->createQueryBuilder('u')
            ->orderBy('u.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function resetUserPosition($userId)
    {
        return $this
            ->createQueryBuilder('u')
            ->update('AppBundle:User', 'u')
            ->set('u.position', ':position')
            ->setParameter('position', 0)
            ->where('u.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->execute();
    }

    public function incrementUsersPosition()
    {
        return $this
            ->getEntityManager()
            ->createQuery('UPDATE AppBundle:User u SET u.position = u.position + 1')
            ->execute();
    }
}
