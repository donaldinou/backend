<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;

class VitelogeAdmin extends Admin
{
    public function initialize()
    {
        parent::initialize();
        $this->maxPageLinks = 5;
/*        print_r( $this->securityHandler->getBaseRole( $this ) );
          die;*/
        
    }

    public function getBaseRoutePattern()
    {
        if ( ! $this->baseRoutePattern ) {
            preg_match('@\\\([^\\\]*)$@', $this->getClass(), $matches );
            if (!$matches) {
                throw new \RuntimeException(sprintf('Please define a default `baseRoutePattern` value for the admin class `%s`', get_class($this)));
            }
            $this->baseRoutePattern = $this->urlize( $matches[1], '-');
        }
        return $this->baseRoutePattern;
    }
    public function getBaseRouteName()
    {
        if (!$this->baseRouteName) {
            preg_match('@\\\([^\\\]*)$@', $this->getClass(), $matches );
            if (!$matches) {
                throw new \RuntimeException(sprintf('Please define a default `baseRoutePattern` value for the admin class `%s`', get_class($this)));
            }
            $this->baseRouteName = 'viteloge_admin_' . $this->urlize( $matches[1] );
        }
        return $this->baseRouteName;
    }
    
}

