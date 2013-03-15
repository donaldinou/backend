<?php

namespace Viteloge\AdminBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\LocationStopword;


class LocationStopwordAdmin extends VitelogeAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier( 'expression' )
            ;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add( 'expression' )
            ;
    }

}
