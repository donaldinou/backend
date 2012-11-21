<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\XmlFeed;

class XmlFeedAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add( 'agence' )
            ->add( 'ismap', null, array( 'required' => false ) )
            ->add( 'url' )
            ->add( 'module', 'choice', array( 'required' => false, 'choices' => XmlFeed::$Modules ) )
            ->add( 'transaction' )
            ->add('inactif', null, array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('url')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->addIdentifier('agence.nom')
            ->addIdentifier('url')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
    }
}