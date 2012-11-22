<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class AgenceAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nom')
            ->add('specif')
            ->add('mail')
            ->add('cp')
            ->add('ville')
            ->add('tel')
            ->add('fax')
            ->add('url')
            ->add('civiliteContact')
            ->add('nomContact')
            ->add('departement')
            ->add('inactive', null, array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('ville')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->addIdentifier('cp')
            ->addIdentifier('ville')
            ->addIdentifier('departement')
            ->addIdentifier('countTraitements')
            ->add( 'active', 'boolean' )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
                ->assertMaxLength(array('limit' => 255))
            ->end()
        ;
    }
}