<?php

namespace Viteloge\AdminBundle\Service;

class TestTraitementService 
{
    private $traitement;
    
    public function __construct( $traitement)
    {
        $this->traitement = $traitement;
    }

    public function run( $type, $source )
    {
        $results = array();

        $results['urls'] = $this->get_url_list( $this->traitement->UrlTraitement );

        switch ( $type )
        {
            case 'R':
                $expressions = array( 'ExpNbBien', 'ExpNbPage', 'ExpPageSuivante', 'ExpLiensfiche' );
                break;
            case 'F':
                $expressions = array();
                break;
        }
        $expression_bag = array();
        foreach ( $expressions as $expression ) {
            if ( ! empty( $this->traitement->$expression ) ) {
                $expression_bag[$expression] = $this->traitement->$expression;
            }
        }
        if ( ! empty( $expression_bag ) ) {
            $tmp_file = tempnam( sys_get_temp_dir(), "testregex_src" );
            $output = fopen( $tmp_file, "w" );
            fwrite( $output, $source );
            fclose( $output );

            $cmd = "/usr/bin/perl " . dirname( __FILE__ ) . "/TestRegex.pl " . escapeshellarg( $tmp_file );

            $process = proc_open( $cmd, array(
                                      0 => array( "pipe", "r" ),
                                      1 => array( "pipe", "w" ) ), $pipes );
            if (is_resource($process)) {

                $config = json_encode( $expression_bag );
                

                fwrite( $pipes[0], $config );
                fclose( $pipes[0] );
                $regex_result = stream_get_contents( $pipes[1] );
                
                $results['expressions'] = json_decode( $regex_result, true );
                fclose( $pipes[1] );
                proc_close( $process );
            }
        }
        

        return $results;
    }



    function get_url_list($model) 
    {
        $URLS = array();
        $out = array();
	
        while( preg_match ( "/\{[^\]]*(\b\d+\b)\.\.(\b\d+\b)[^\]]*\}/", $model, $out) ) {
            $i = 0;
            $new_items = array();
            $un = $out[1];
            $deux = $out[2];
            $strlen = strlen($un);
            if($un < $deux) {
                for($i = $un; $i <=$deux; $i++) {
                    if(strlen($i) < $strlen) 
                        $i = str_pad($i,$strlen,"0",STR_PAD_LEFT);
                    array_push($new_items, $i);
                }
            } else {
                for($i = $un; $i >=$deux; $i--) {
                    array_push($new_items, $i);
                }
            }
            $value = join($new_items, ",");
            $model = preg_replace ("/(\{[^\]]*)(\b\d+\b\.\.\b\d+\b)([^\]]*\})/e", "'\\1'.'$value'.'\\3'",$model);
        }
        # ---------------------
        
        # --- génération des urls ---
        preg_match ( "/(\{[^\}]+\})/", $model, $out);
        if(!empty($out[1])) { 
            $item = $out[1];
            $list = substr($item,1,strlen($item)-2);
            $item = preg_quote($item, "/");
            $values = explode(",",$list);
            $model = preg_replace ('/'.$item.'/', "___ITEM___",$model);
            
            foreach ($values as $value) {
                $url = $model;
                $url = preg_replace ('/___ITEM___/', $value,$url);
                preg_match ( "/(\{[^\}]+\})/", $model, $out);
                if(!empty($out[1])) {
                    $next_exp = $out[1];
                    $next_urls = get_url_list($url);
                    foreach ($next_urls as $url) {
                        array_push($URLS, $url);
                    }
                } else {
                    array_push($URLS, $url);
                }
            }
        } else {
            array_push($URLS, $model);
        }
        # ---------------------
        return $URLS;
    }

    
}
