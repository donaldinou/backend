<?php

namespace Viteloge\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\FormBuilder;

/**
 * @Route("/agence")
 */
class AgenceController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/{id}/stats")
     * @Template()
     */
    public function statsAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $agence_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Agence' );
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $annonce_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Annonce' );
        $feed_repo = $em->getRepository('Viteloge\AdminBundle\Entity\XmlFeed' );
        $agence = $agence_repo->find( $id );

        $cycles = array();
        foreach ( $agence->traitements as $traitement ) {
            $cycles[$traitement->id] = array_map( function($cycle){
                    return array(
                        $cycle->fin->format('Y-m-d'),
                        $cycle->nbAnnonce,
                        $cycle->nbAnnonceInsert,
                        $cycle->nbAnnonceDelete
                    );
                }, $traitement_repo->getCycles( $traitement, true ) );
            
        }
        $cycles_xml = array();
        foreach ( $agence->xml_feeds as $xml_feed ) {
            $cycles_xml[$xml_feed->id] = array_map( function($cycle){
                    return array(
                        $cycle->fin->format('Y-m-d'),
                        $cycle->nbAnnonce,
                        $cycle->nbAnnonceInsert,
                        $cycle->nbAnnonceDelete
                    );
                }, $feed_repo->getCycles( $xml_feed, true ) );
            
        }

        
        return array(
            'agence' => $agence,
            'cycles' => $cycles,
            'cycles_xml' => $cycles_xml,
            'admin_pool' => $this->container->get('sonata.admin.pool'),
        );
    }

    /**
     * @Route("/{id}/stats/detail")
     * @Template()
     */
    public function statsDetailAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $agence_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Agence' );
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $annonce_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Annonce' );
        $feed_repo = $em->getRepository('Viteloge\AdminBundle\Entity\XmlFeed' );
        $agence = $agence_repo->find( $id );

        $stats_insee = $annonce_repo->getCountByInsee( $agence );
        if ( count( $stats_insee ) > 0 ) {
            $repartition = array(
                'labels' => array_map( function($stat) {
                        return join( ' : ', array( $stat['fullName'], $stat['nbAnnonces'] ) );
                    }, $stats_insee ),
                'data' => array_map( function($stat){
                        return $stat['nbAnnonces'];
                    }, $stats_insee )
            );
        }

        return array(
            'admin_pool' => $this->container->get('sonata.admin.pool'),            
            'stats' => array(
                'exported' => $annonce_repo->getCountExported( null, $agence ),
                'all' => $annonce_repo->getCountMisc( $agence ),
                'nbvisits' => $agence_repo->getVisits( $agence ),
                'repartition' => $repartition
            )
        );
    }
    
    
    /**
     * @Route("/{id}/report")
     */
    public function reportAction( $id )
    {
        $reporter = $this->get('viteloge.admin.report_generator');

        $response = new Response();
        $response->setCharset( "iso-8859-1" );
        $response->setContent( $reporter->run( $id ) );
        
        return $response;
    }

    /**
     * @Route( "/img/{path}" )
     */
    public function imgAction( $path )
    {
        $reporter = $this->get('viteloge.admin.report_generator');

        if ( $reporter->pathIsReportImage( $path ) ) {
            $response = new Response();
            $response->setContent( $reporter->getImage( $path ) );
            $response->headers->set('Content-Type', 'image/png');
        } else {
            throw $this->createNotFoundException( "No such file" );
        }
        
        return $response;
    }

    /**
     * @Route("/{id}/logo")
     */
    public function logoAction( Request $request, $id )
    {
        $fb = $this->container->get('form.factory')->createNamedBuilder('agence_logo', 'form', null, array() );
        $form = self::buildLogoForm( $fb );
        if ( $request->isMethod( 'POST' ) ) {
            $em =  $this->get('doctrine.orm.entity_manager');
            $agence_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Agence' );
            $agence = $agence_repo->find( $id );

            $form->bind( $request );
            if ( $form->isValid() ) {
                $file = $form['logo']->getData();

                $logo_manager = $this->get( 'viteloge.admin.logo_manager' );

                try 
                {
                    if ( is_null( $file ) ) {
                        $this->get('logger')->info( "Removing logo" );
                        $logo_manager->removeLogo( $agence );
                    } else if ( $file->isValid() ) {
                        $this->get('logger')->info( "Handling upload" );
                        if ( 1 == $form['resize']->getData() ) {
                            $resize = array(
                                'width' => $form['width']->getData(),
                                'height' => $form['height']->getData()
                            );
                        } else {
                            $resize = false;
                        }
                        print_r( $resize );
                        print_r( $form->getData() );
                        $logo_manager->updateLogo( $agence, $file, $resize );
                    } else {
                        $this->get('logger')->info( "invalid upload ?" );
                    }
                    
                } catch ( \S3Exception $e ) {
                    $this->get('logger')->info( $e->getMessage() );
                    $this->get('session')->setFlash('error', $e->getMessage() );
                }
            
                return $this->redirect( $this->generateUrl( 'admin_viteloge_admin_agence_edit', array( 'id' => $id )  ) );
            }
        }
        return new Response( "hello" );
    }

    public static function buildLogoForm( FormBuilder $form_builder )
    {
        $form_builder->setData( array( 'resize' => false, 'width' => 50, 'height' => 50 ) );
        $form_builder->add( 'logo', 'file', array( 'required' => false ) );
        $form_builder->add( 'resize', 'checkbox', array( 'required' => false ) );
        $form_builder->add( 'width', 'text',  array( 'required' => false ) );
        $form_builder->add( 'height', 'text', array( 'required' => false ) );
        
        return $form_builder->getForm();
    }
}
