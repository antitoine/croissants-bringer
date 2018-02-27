<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Participation;
use AppBundle\Enum\ParticipationStatusEnum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * ParticipationRepository
 */
class ParticipationRepository extends EntityRepository
{
    /**
     * @return Participation|null
     */
    public function findLastParticipation()
    {
        try {
            return $this
                ->createQueryBuilder('p')
                ->orderBy('p.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param \DateTime $date
     * @return array of user id
     */
    public function findExcludedUsers(\DateTime $date)
    {
        return $this
            ->createQueryBuilder('p')
            ->select('IDENTITY(p.user)')
            ->where('p.date = :date')
            ->setParameter('date', $date)
            ->andWhere('p.status = :status')
            ->setParameter('status', ParticipationStatusEnum::STATUS_REFUSED)
            ->getQuery()
            ->getArrayResult();
    }
}
