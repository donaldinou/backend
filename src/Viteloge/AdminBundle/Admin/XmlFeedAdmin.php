<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Viteloge\AdminBundle\Entity\XmlFeed;

if ( ! is_callable('Viteloge\AdminBundle\Admin\array_to_array') ){
    function array_to_array( $src )
    {
        $dest = array();
        foreach ( $src as $k ) {
            $dest[$k] = $k;
        }
        return $dest;
    }
}


class XmlFeedAdmin extends VitelogeAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add( 'agence', 'sonata_type_model_list' )
            ->add( 'ismap', null, array( 'required' => false ) )
            ->add( 'url' )
            ->add( 'module', 'choice', array( 'required' => false, 'choices' => array_to_array( XmlFeed::$Modules ) ) )
            ->add( 'transaction', null, array( 'required' => false ) )
            ->add('inactif', null, array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add( 'url' )
            ->add( 'agence.nom' )
            ->add( 'inactif' )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier( 'nomAgence' )
            ->addIdentifier( 'url' )
            ->addIdentifier( 'actif', 'boolean' )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
    }

    public function getTemplate( $name )
    {
        switch ( $name ) 
        {
            case 'edit':
                return 'VitelogeAdminBundle:XmlFeed:edit.html.twig';
                break;
            default:
                return parent::getTemplate( $name );
        }
    }
    protected function configureRoutes(RouteCollection $collection)
    {
        $id_param = $this->getRouterIdParameter();
        $collection->add( 'stats', $id_param . "/stats" );
    }

    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $id_agence = $this->request->get('idAgence');
        if ( ! empty( $id_agence ) ) {
            $agence_repo = $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository( 'Viteloge\AdminBundle\Entity\Agence' );
            $agence = $agence_repo->find( $id_agence );
            $instance->agence = $agence;
        }
        return $instance;
    }
}
