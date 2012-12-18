<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

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
            ->where( 'traitement.Exclus = 1' )
            ->addOrderBy( 'agence.idPrivilege', 'DESC' );
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

    public function getCycles( $traitement, $full = false )
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select( 'cycle' )
            ->from( 'Viteloge\AdminBundle\Entity\Cycle', 'cycle' )
            ->where( 'cycle.traitement = :traitement' )
            ->addOrderBy( 'cycle.fin', 'ASC' )
            ->setParameter( 'traitement', $traitement );
        if ( ! $full ) {
            $qb->andWhere( 'cycle.fin > :date' )
                ->setParameter( 'date', new \DateTime( '6 months ago' ) );
        }
        
        return $qb->getQuery()->getResult();
    }

    public function getLastContentInfo( $traitement, $full = false ) 
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult( 'resultSize', 'resultSize' );
        $rsm->addScalarResult( 'downloadedAt', 'downloadedAt' );

        $fields = "resultSize, downloadedAt";
        if ( $full ) {
            $rsm->addScalarResult( 'url', 'url' );
            $rsm->addScalarResult( 'url2', 'url2' );
            $rsm->addScalarResult( 'result', 'result' );
            $fields .= ", url, url2, result";
        }
        

        $query = $this->_em->createNativeQuery(
            "SELECT " . $fields . " FROM contenu_traitement WHERE idTraitement = :idTraitement", $rsm )
            ->setParameter( 'idTraitement', $traitement->id );
        $x = $query->getScalarResult();
        if ( count( $x ) > 0 ) {
            return $x[0];
        }
        return null;
    }
}
