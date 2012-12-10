<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TraitementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TraitementRepository extends EntityRepository
{
    public function getExclus()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select( 'traitement' )
            ->from( 'Viteloge\AdminBundle\Entity\Traitement', 'traitement' )
            ->leftJoin( 'traitement.agence', 'agence' )
            ->leftJoin( 'traitement.expression', 'expression' )
            ->where( 'traitement.Exclus = 1' );
        return $qb->getQuery()->getResult();
    }

    public function resetErrors( $traitement )
    {
        $dbh = $this->_em->getConnection();
        $nb = $dbh->executeUpdate(
            "DELETE FROM erreurs WHERE IdTraitement = ?",
            array( $traitement->id )
        );
        return $nb;        
    }

    public function getCycles( $traitement )
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select( 'cycle' )
            ->from( 'Viteloge\AdminBundle\Entity\Cycle', 'cycle' )
            ->where( 'cycle.traitement = :traitement' )
            ->addOrderBy( 'cycle.fin', 'ASC' )
            ->setParameter( 'traitement', $traitement );
        return $qb->getQuery()->getResult();
    }
}
