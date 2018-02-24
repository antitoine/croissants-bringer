<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * @return User|null
     */
    public function findCroissantsBringer()
    {
        try {
            return $this
                ->createQueryBuilder('u')
                ->orderBy('u.position', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param $userId string|integer the user ID
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    public function incrementUsersPosition()
    {
        return $this
            ->getEntityManager()
            ->createQuery('UPDATE AppBundle:User u SET u.position = u.position + 1')
            ->execute();
    }
}
