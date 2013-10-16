<?php

namespace Viteloge\AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\Query\ResultSetMapping;

use Viteloge\AdminBundle\Entity\CityFilter;

/**
 * @Route("/citystats/")
 */
class CityStatsController extends Controller 
{
    /**
     * @Route()
     * @Template
     */
    public function indexAction( Request $request )
    {
        $form = $this->createCityFilterForm();

        $form->bind($request);

        $data = null;
        
        if ( $form->isValid() ) {
            $filter = $form->getData();
            $data = $this->getStatsForCity( $filter );
        }
        
        return array(
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'filter_form' => $form->createView(),
            'data' => $data
        );
    }
    /**
     * @Route("complete")
     */
    public function cityCompleteAction( Request $request )
    {
        $em = $this->get( 'doctrine.orm.entity_manager' );

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult( 'codeInsee', 'codeInsee');
        $rsm->addScalarResult( 'nom', 'nom');
        $rsm->addScalarResult( 'cp', 'cp');
        $rsm->addScalarResult( 'codeDepartement', 'codeDepartement');

        $sql = <<<EOF
SELECT nom, cp, codeInsee, codeDepartement, MATCH( nom ) AGAINST ( ? IN BOOLEAN MODE ) AS score
FROM insee_communes
WHERE niveauGeo = 'COM'            
HAVING score > 0
ORDER BY score DESC, population DESC
LIMIT 0,10            
EOF;
        $query = $em->createNativeQuery( $sql, $rsm );

        $term = trim( $request->get( 'term' ) );
        $words = preg_split( "/-|\s+/", $term );
        $term = implode( " ", $words ) . '*';
        
        $query->setParameter( 1, $term );
        
        return new JsonResponse( $query->getScalarResult() );
    }

    protected function createCityFilterForm()
    {
        $form = $this->createFormBuilder( new CityFilter() )
            ->add( 'ville_id', 'hidden' )
            ->add( 'ville' )
            ->getForm();
        return $form;
    }


    protected function getStatsForCity( $filter )
    {
        $em = $this->get( 'doctrine.orm.entity_manager' );

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult( 'pl', 'pl', 'int');
        $rsm->addScalarResult( 'cl', 'cl', 'int' );

        $sql = <<<EOF
SELECT properties pl, clicks cl
FROM stats_cities
WHERE codeInsee = ?
EOF;
        $query = $em->createNativeQuery( $sql, $rsm );

        $query->setParameter( 1, $filter->getVilleId() );

        return $query->getScalarResult();
    }
    
}
