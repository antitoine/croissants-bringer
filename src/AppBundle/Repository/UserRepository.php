<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Enum\UserStatusEnum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param $excludedUserIdList array of user id
     * @return User|null
     */
    public function findCroissantsBringer($excludedUserIdList = [])
    {
        try {
            $qb = $this->createQueryBuilder('u')
                ->orderBy('u.position', 'DESC')
                ->where('u.participant = :participant')
                ->andWhere('u.status = :status')
                ->setParameter('participant', true)
                ->setParameter('status', UserStatusEnum::STATUS_EMPLOYED)
                ->setMaxResults(1);

            if (count($excludedUserIdList) > 0) {
                $qb->andWhere($qb->expr()->notIn('u.id', ':users'))
                    ->setParameter('users', $excludedUserIdList);
            }

            return $qb->getQuery()->getOneOrNullResult();
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
            ->createQuery('UPDATE AppBundle:User u SET u.position = u.position + 1 WHERE u.status = :status')
            ->setParameter('status', UserStatusEnum::STATUS_EMPLOYED)
            ->execute();
    }
}
