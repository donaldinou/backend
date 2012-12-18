<?php

namespace Viteloge\AdminBundle\Service;

class ReportGeneratorService
{

    private $lib_path;
    
    public function __construct( $path )
    {
        $this->lib_path = $path;
    }

    public function run( $agence_id)
    {
        
        $params = array( "/usr/bin/perl" );
        foreach ( array( "crawler", "tasks/lib" ) as $ipath ) {
            $params[] = "-I" . $this->lib_path . "/" . $ipath;
        }
        $params[] = "-x" . $this->lib_path . "/tasks/reports";

        $params[] = __DIR__ . "/report.pl";
        $params[] = $agence_id;

        $cmd = join( " ", $params );
        $process = proc_open( $cmd, array(
                                  0 => array( "pipe", "r" ),
                                  1 => array( "pipe", "w" )/*,
                                  2 => array( "file", "/tmp/error.txt", "w")*/ ), $pipes );
        $result = "";
        if (is_resource($process)) {
            
            fclose( $pipes[0] );
            $result = stream_get_contents( $pipes[1] );
            fclose( $pipes[1] );
            proc_close( $process );
        }
        return $result;
    }
    public function pathIsReportImage( $img_path )
    {
        return file_exists( $this->getImagePath( $img_path ) );
    }
    public function getImage( $img_path )
    {
        return file_get_contents( $this->getImagePath( $img_path ) );
    }

    private function getImagePath( $filename )
    {
        if ( ! preg_match( "/^[a-z-]*\.png$/", $filename ) ) {
            throw new \Exception( "Invalid path" );
        }
        return join( "/", array( $this->lib_path, "tasks/reports/img", $filename ) );
    }
    
}
