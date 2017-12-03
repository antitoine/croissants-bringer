<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ParticipationRepository
 */
class ParticipationRepository extends EntityRepository
{
    public function findLastParticipation()
    {
        return $this
            ->createQueryBuilder('p')
            ->orderBy('p.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
