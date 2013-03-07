<?php

namespace Viteloge\AdminBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\TypeBien;


class TypeBienAdmin extends VitelogeAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier( 'code' )
            ->addIdentifier( 'libelle' );
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add( 'code', null, array( 'label' => 'form.label_code_type') )
            ->add( 'libelle' )
//            ->add( 'regexs', 'sonata_type_collection', array( 'edit' => 'inline' ) )
            ;
    }

}
