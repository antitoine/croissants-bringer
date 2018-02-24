<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Participation;
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
                ->orderBy('p.date', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
