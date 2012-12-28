<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\XmlFeed;

class XmlFeedAdmin extends VitelogeAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add( 'agence', 'sonata_type_model_list' )
            ->add( 'ismap', null, array( 'required' => false ) )
            ->add( 'url' )
            ->add( 'module', 'choice', array( 'required' => false, 'choices' => XmlFeed::$Modules ) )
            ->add( 'transaction', null, array( 'required' => false ) )
            ->add('inactif', null, array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add( 'url' )
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
}