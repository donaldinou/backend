<?php

namespace Viteloge\AdminBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\TypeBien;


class TypeBienRegexAdmin extends VitelogeAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier( 'code' )
            ->addIdentifier( 'score' )
            ->addIdentifier( 'expression' )
            ->addIdentifier( 'rank' )
            ;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add( 'code', null, array( 'label' => 'form.label_type_bien') )
            ->add( 'score' )
            ->add( 'expression' )
            ->add( 'rank' )
            ;
    }
}
