<?php

namespace Viteloge\AdminBundle\Service;

use Viteloge\AdminBundle\Entity\Agence;

require_once( __DIR__ . '/../libs/S3.php' );

class LogoManagerService
{
    private $s3;
    private $bucket;
    private $endpoint;
    
    public function __construct( $access_key, $secret_key, $bucket, $endpoint )
    {
        \S3::setExceptions( true );
        $this->s3 = new \S3( $access_key, $secret_key, false, $endpoint );
        $this->bucket = $bucket;
        $this->endpoint = $endpoint;
    }

    public function hasLogo( Agence $agence )
    {
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $this->logoPath( $agence ) );
        curl_setopt( $curl, CURLOPT_HEADER, true);
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_exec( $curl );
        $return_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        curl_close( $curl );
        return 200 == $return_code;
    }

    public function logoPath( Agence $agence )
    {
        $host = $this->bucket;
        if ( preg_match( '/test/', $host ) ) {
            $host = $host . ".s3.amazonaws.com";
        }
        return 'http://' . $host . '/' . self::formatRemoteFile( $agence );
    }
    
    
    public function removeLogo( Agence $agence )
    {
        $this->s3->deleteObject( $this->bucket, self::formatRemoteFile( $agence ) );
    }
    
    public function updateLogo( Agence $agence, $file, $resize )
    {
        $path = $file->getPathname();
        if ( false !== $resize ) {
            $image = new \Imagick();
            $image->readImage( $path );
            $imageprops = $image->getImageGeometry();

            $image->resizeImage( $resize['width'], $resize['height'], \imagick::FILTER_BOX, 0 );
            $image->setImageFormat( "gif" );
            
            $image->writeImage( $path );
            $image->clear();
            $image->destroy();

        }
        return $this->s3->putObjectFile( $path,
                                         $this->bucket,
                                         self::formatRemoteFile( $agence ),
                                         \S3::ACL_PUBLIC_READ );
    }

    private static function formatRemoteFile( Agence $agence )
    {
        return sprintf( "logos/agence.%d.gif", $agence->id );
    }
}
