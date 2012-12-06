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
        preg_match ( "/([^\?#]*)\?{0,1}([^#]*)#{0,1}(.*)/", $results['urls'][0], $match_array);
        $this->full_url = $results['urls'][0];
        $base_url = $match_array[1];
            


        switch ( $type )
        {
            case 'R':
                $expressions = array( 'ExpNbBien', 'ExpNbPage', 'ExpPageSuivante' );
                $expressions_array = array( 'ExpLiensFiche' );
                // just to let us handle them later
                $expressions_urls = array( 'ExpPageSuivante' );
                break;
            case 'F':
                $expressions = array( 'ExpUrlElements', 'ExpTypeLogement', 'ExpNbChambre', 'ExpSurface', 'ExpUrlPhoto', 'ExpPiece', 'ExpPrix', 'ExpVille', 'ExpArrondissement', 'ExpCp', 'ExpDescription', 'ExpAgence' );
                $expressions_array = array();
                $expressions_urls = array();
                break;
        }
        $expression_bag = array();
        $expressions_empty = array();
        foreach ( $expressions as $expression ) {
            if ( empty( $this->traitement->$expression ) ) {
                $expressions_empty[$expression] = array( 'empty' => true );
            } else {
                $expression_bag[$expression] = array( "expr" => $this->traitement->$expression, "array" => false );
            }
        }
        foreach ( $expressions_array as $expression ) {
            if ( ! empty( $this->traitement->$expression ) ) {
                $expression_bag[$expression] = array( "expr" => $this->traitement->$expression, "array" => true );
            }
        }
        if ( ! empty( $expression_bag ) ) {
            $results['expressions'] = $this->callPerlTester( $source, $expression_bag, $expressions_array );
        

            if ( array_key_exists( 'ExpLiensFiche', $results['expressions'] )
                 && count( $results['expressions']['ExpLiensFiche']['value'] ) > 0 ) {

                $results['expressions']['ExpLiensFiche']['value'] = array_unique( $results['expressions']['ExpLiensFiche']['value'] );
                foreach ( $results['expressions']['ExpLiensFiche']['value'] as $lien ) {
                    $results['liens_fiches'][] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlFicheTraitement, $lien ), $base_url );
                }
            }
            if ( array_key_exists( 'ExpUrlPhoto', $results['expressions'] ) && ! empty( $results['expressions']['ExpUrlPhoto']['value'] ) ) {
                $results['expressions']['ExpUrlPhoto']['photo'] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlPhoto, $results['expressions']['ExpUrlPhoto']['value'] ), $base_url );                
            }
            
            foreach ( $expressions_urls as $expression ) {
                if ( ! empty( $results['expressions'][$expression]['value'] ) ) {
                    $results['expressions'][$expression]['url'] = true;
                    $results['expressions'][$expression]['value'] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlPageSuivante, $results['expressions'][$expression]['value'] ), $base_url );
                }
            }
        } else {
            $results['expressions'] = array();
        }
        $results['expressions'] = array_merge( $results['expressions'], $expressions_empty );
        foreach ( $results['expressions'] as $name => $result ) {
            if ( array_key_exists( $name, $expression_bag ) ) {
                $results['expressions'][$name]['expr'] = $expression_bag[$name]['expr'];
            }
        }
        

        return $results;
    }


    private function callPerlTester( $source, $expression_bag, $expressions_array )
    {
        if ( preg_match("/^((ht|f)tp(s?))\:\/\//", $source ) ) {
            $source = $this->download_file( $source );
        }
        
        $tmp_file = tempnam( sys_get_temp_dir(), "testregex_src" );
        $output = fopen( $tmp_file, "w" );
        fwrite( $output, $source );
        fclose( $output );

        $cmd = "/usr/bin/perl " . dirname( __FILE__ ) . "/TestRegex.pl " . escapeshellarg( $tmp_file );

        $process = proc_open( $cmd, array(
                                  0 => array( "pipe", "r" ),
                                  1 => array( "pipe", "w" )/*,
                                  2 => array( "file", "/tmp/error.txt", "w")*/ ), $pipes );
        $results = array();
        if (is_resource($process)) {
            
            $config = json_encode( $expression_bag );
            
            fwrite( $pipes[0], $config );
            fclose( $pipes[0] );
            $regex_result = stream_get_contents( $pipes[1] );

            $decoded_result = json_decode( $regex_result, true );
            
            foreach ( $decoded_result as $id => $output ) {
                $results[$id] = array( 'value' => $output, 'array' => in_array( $id, $expressions_array ) );
            }
            fclose( $pipes[1] );
            proc_close( $process );
        }
        unlink( $tmp_file );
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
                    $next_urls = $this->get_url_list($url);
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


    private function build_custom_url($url, $item = NULL) 
    {
        if(empty($url)) $url = "¤";
        
        $item = preg_replace ("/\{/", "~%7B~" ,$item);
        $item = preg_replace ("/\}/", "~%7D~" ,$item);
        
        if($item !== NULL) $url = preg_replace ("/¤/", $item ,$url);
        $mask = "/(\{[^\}]*\})/";
        preg_match ( $mask, $url, $out);
        while(!empty($out[1]))
        {
            $var = $out[1];
            $FULL_URL = $this->full_url;
            $formule = substr($var,1,strlen($var) - 2);
            eval("\$result = \$this->" . $formule .";"); 
            //$cmd = sprintf('perl -e "print %s"', preg_replace('/"/', '\\"', $formule));
            //$result = exec($cmd);
            $url = preg_replace ( "/".preg_quote($var, "/")."/i", $result, $url);
            preg_match ( $mask, $url, $out);
        }
        
        $url = preg_replace ("/\\\{|~%7B~/", "{" ,$url);
        $url = preg_replace ("/\\\}|~%7D~/", "}" ,$url);
        $url = html_entity_decode($url);
        
        //$url = preg_replace ("/\s*(\r\n|\r|\n)\s*/", "" ,$url);
        return $url;
    }
    private function make_absolute($rel_uri, $base, $REMOVE_LEADING_DOTS = true) {
        if (preg_match("'^[^:]+://'", $rel_uri)) { 
            return $rel_uri; 
        }
        preg_match("'^([^:]+://[^/]+)/'", $base, $m);
        $base_start = $m[1];
        if (preg_match("'^/'", $rel_uri)) {
            return $base_start . $rel_uri;
        }
        $base = preg_replace("{[^/]+$}", '', $base);
        $base .= $rel_uri;
        $base = preg_replace("{^[^:]+://[^/]+}", '', $base);
        $base_array = explode('/', $base);
        if (count($base_array) and!strlen($base_array[0]))
            array_shift($base_array);
        $i = 1;
        while ($i < count($base_array)) {
            if ($base_array[$i - 1] == ".") {
                array_splice($base_array, $i - 1, 1);
                if ($i > 1) $i--;
            } elseif ($base_array[$i] == ".." and $base_array[$i - 1]!= "..") {
                array_splice($base_array, $i - 1, 2);
                if ($i > 1) {
                    $i--;
                    if ($i == count($base_array)) array_push($base_array, "");
                }
            } else {
                $i++;
            }
        }
/*        if ( count($base_array) > 0 ) {
            if ( $base_array[-1] == "." ){
                $base_array[-1] = "";
            }
            }*/
        
		
        if ($REMOVE_LEADING_DOTS) {
            while (count($base_array) and preg_match("/^\.\.?$/", $base_array[0])) {
                array_shift($base_array);
            }
        }
        return($base_start . '/' . implode("/", $base_array));
    } 
    private function download_file( $url )
    {
        $post_vars = false;
        $parts = explode("#", $url);
        if(!empty($parts[1])) {
            $post_vars 	= $parts[1];
            $url 		= $parts[0];
        }

        $ch = curl_init( $url );

        $headers = array();
        $headers[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $headers[] = "Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3";	
        //$headers[] = "Accept-Encoding: gzip, deflate";		
        $headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);


        curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        
        if( $post_vars ) {
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_vars );
        }

        $data = curl_exec( $ch );
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE );
        if ( $return_code != 200 ) {
            return '';
        }
        return $data;
    }
    private function clean_url($url) {
        $args = func_get_args();
        unset($args[0]);
        foreach( $args as $item ) {
            $url = preg_replace("/((?<=\?|#)|&)$item=[^&]*/si","",$url);
        }
        return $url;
    }

}