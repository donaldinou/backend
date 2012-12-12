<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;

class VitelogeAdmin extends Admin
{
    public function initialize()
    {
        parent::initialize();
        $this->maxPageLinks = 5;
        
    }
}

