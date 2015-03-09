<?php

namespace Viteloge\AdminBundle\Service;

use Viteloge\AdminBundle\Entity\ExpressionUnderTest;

class TestTraitementService 
{
    private $traitement;

    public $downloadedSource = null;
    public $expects_array_results = false;
    
    
    public function __construct( $traitement)
    {
        $this->traitement = $traitement;
    }

    public function clearCookies()
    {
        $cookies_file = $this->getCookiesFile();
        if ( file_exists( $cookies_file ) ) {
            return @unlink( $cookies_file );
        }
        return false;
    }
    
    
    public function run( $type, $source )
    {
        $results = array();

        $results['urls'] = $this->get_url_list( $this->traitement->UrlTraitement );
        if ( preg_match("/^((ht|f)tp(s?))\:\/\//", $source ) ) {
            $this->setUrls( $source );
        } else {
            $this->setUrls( $results['urls'][0] );
        }
            
        if ( empty( $source ) ) {
            $source = $this->full_url;
        }
            

        switch ( $type )
        {
            case 'R':
                $expressions = array( 'ExpNbBien', 'ExpNbPage', 'ExpPageSuivante' );
                $expressions_array = array( 'ExpLiensFiche' );
                // just to let us handle them later
                $expressions_urls = array( 'ExpPageSuivante' );
                break;
            case 'F':
                $expressions = array( 'ExpUrlElements', 'ExpTypeLogement', 'ExpNbChambre', 'ExpSurface', 'ExpUrlPhoto', 'ExpPiece', 'ExpPrix', 'ExpVille', 'ExpArrondissement', 'ExpCP', 'ExpDescription', 'ExpAgence', 'SplitResultAnnonce' );
                $expressions_array = array();
                $expressions_urls = array();
                $this->expects_array_results = ! empty( $this->traitement->SplitResultAnnonce );
                break;
        }
        $expression_bag = array();
        $expressions_empty = array();
        $number_of_expressions = 0;
        foreach ( $expressions as $expression ) {
            $expression_bag[$expression] = new ExpressionUnderTest( $expression, $this->traitement );
            if ( $expression_bag[$expression]->isValid() ) {
                $number_of_expressions++;
            }   
        }
        foreach ( $expressions_array as $expression ) {
            $expression_bag[$expression] = new ExpressionUnderTest( $expression, $this->traitement, true );
            if ( $expression_bag[$expression]->isValid() ) {
                $number_of_expressions++;
            }   
        }
        if ( $number_of_expressions > 0 ) {
            $results['expressions'] = $this->callPerlTester( $source, $expression_bag );
        

            if ( 'F' == $type ) {
                foreach ( $results['expressions'] as &$result ) {
                    if ( array_key_exists( 'ExpUrlPhoto', $result ) && ! empty( $result['ExpUrlPhoto']['value'] ) ) {
                        $result['ExpUrlPhoto']['value'] = str_replace( array( '$'), array( '%24' ), $result['ExpUrlPhoto']['value'] );
                        $result['ExpUrlPhoto']['photo'] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlPhoto, $result['ExpUrlPhoto']['value'] ), $this->base_url );                
                    }
                }
            } elseif ( 'R' == $type ) {
                foreach ( $expressions_urls as $expression ) {
                    if ( ! empty( $results['expressions'][0][$expression]['value'] ) ) {
                        $results['expressions'][0][$expression]['url'] = true;
                        $results['expressions'][0][$expression]['value'] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlPageSuivante, $results['expressions'][0][$expression]['value'] ), $this->base_url );
                    }
                }

                if ( array_key_exists( 'ExpLiensFiche', $results['expressions'][0] )
                     && count( $results['expressions'][0]['ExpLiensFiche']['value'] ) > 0 ) {
                    
                    $results['expressions'][0]['ExpLiensFiche']['value'] = array_unique( $results['expressions'][0]['ExpLiensFiche']['value'] );
                    foreach ( $results['expressions'][0]['ExpLiensFiche']['value'] as $lien ) {
                        $results['liens_fiches'][] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlFicheTraitement, $lien ), $this->base_url );
                    }
                }
                if ( 'R' == $this->traitement->TypeUrlSortieTraitement & ! empty( $this->traitement->ModelUrlResultatTraitement ) ){
                    $nb_biens_total = $results['expressions'][0]['ExpNbBien']['value'];
                    $nb_biens_page = count( $results['liens_fiches'] );
                    $nb_pages = ceil( $nb_biens_total / $nb_biens_page );
                    $max = $nb_pages > 10 ? 10 : $nb_pages;
                    $urls_by_page = array();
                    for( $i = 2; $i <= $max; $i++ ) {
                        $urls_by_page[] = $this->make_absolute( $this->build_custom_url( $this->traitement->ModelUrlResultatTraitement, $i ), $this->full_url );
                    }
                    $results['liens_pages_suivantes_nombre'] = $urls_by_page;
                }
            }
        } else {
            $results['expressions'] = array();
        }
        foreach ( $results['expressions'] as &$result ) {
            $result = array_merge( $result, $expressions_empty );
            foreach ( $result as $name => $result_value ) {
                if ( array_key_exists( $name, $expression_bag ) ) {
                    $result[$name]['expr'] = $expression_bag[$name];
                }
            }
        }
        return array( 'results' => $results, 'info_expressions' => $expression_bag );
    }


    private function callPerlTester( $source, $expression_bag )
    {
        $results = array();

        $possible_charset = "";
        if ( preg_match("/^((ht|f)tp(s?))\:\/\//", $source ) ) {
            $source = $this->download_file( $source );
            $this->downloadedSource = $source;
            if ( is_array( $source ) ) {
                $possible_charset = " " . $source[1];
                $source = $source[0];
                if ( 'UTF-8' != strtoupper( $possible_charset ) ) {
                    // some people mix charsets for one reason or the other
                    $source = preg_replace( '/[\xE9\xE8]/', 'e', $source );
                    $this->downloadedSource = @iconv( $possible_charset, 'UTF-8', $source );
                }
            }
        }
        
        $tmp_file = tempnam( sys_get_temp_dir(), "testregex_src" );
        $output = fopen( $tmp_file, "w" );
        fwrite( $output, $source );
        fclose( $output );

        $cmd = "/usr/bin/perl " . dirname( __FILE__ ) . "/TestRegex.pl " . escapeshellarg( $tmp_file ) . $possible_charset;

        $process = proc_open( $cmd, array(
                                  0 => array( "pipe", "r" ),
                                  1 => array( "pipe", "w" )/*,
                                  2 => array( "file", "/tmp/error.txt", "w")*/ ), $pipes );
        $expression_config = $this->generateExpressionConfig( $expression_bag );
        if (is_resource($process)) {
            
            $config = json_encode( $expression_config );

            fwrite( $pipes[0], $config );
            fclose( $pipes[0] );
            $regex_result = stream_get_contents( $pipes[1] );

            $decoded_result = json_decode( $regex_result, true );

            if ( $this->isHandlingArrayOfResults() ) {
                foreach ( $decoded_result as $individual_result ) {
                    $results[] = $this->prep_result( $individual_result, $expression_bag );
                }
            } else {
                $results[] = $this->prep_result( $decoded_result, $expression_bag );
            }
            
            fclose( $pipes[1] );
            proc_close( $process );
        }
        unlink( $tmp_file );
        return $results;
    }

    private function prep_result( $result, $expressions ) 
    {
        if ( array_key_exists( '_errors', $result ) ) {
            $errors = $result['_errors'];
            unset( $result['_errors'] );
            foreach ( $errors as $id => $msg ) {
                $results[$id] = array( 'error' => $msg );
            }
        }   
        foreach ( $result as $id => $output ) {
            $results[$id] = array( 'value' => $output );
        }
        return $results;
    }
    
    
    private function generateExpressionConfig( $expression_bag )
    {
        $config = array();
        foreach ( $expression_bag as $name => $expression ) {
            if ( $expression->isValid() ) {
                $config[$name] = array(
                    'expr' => $expression->getValue(),
                    'array' => $expression->isArray()
                );
            }
        }
        return $config;
    }
    

    private function isHandlingArrayOfResults()
    {
        return $this->expects_array_results;
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

    public static function build_single_custom_url( $traitement, $url )
    {
        $tester = new TestTraitementService( $traitement );
        return $tester->build_custom_url( $url );
            
    }
    
    

    protected function build_custom_url($url, $item = NULL) 
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
            $BASE_URL = $this->base_url;
            $BASE_URL_GET 	= $this->base_url_get;
            $BASE_URL_POST  = $this->base_url_post;


            
            $formule = substr($var,1,strlen($var) - 2);
            if ( preg_match( '/^[a-z]/', $formule[0] ) ){
                $prefix = '$this->';
            } else {
                $prefix = '';
            }
            $to_be_evaled = "\$result = " . $prefix . $formule .";";
            eval( $to_be_evaled ); 
            //$cmd = sprintf('perl -e "print %s"', preg_replace('/"/', '\\"', $formule));
            //$result = exec($cmd);
            $url = preg_replace ( "/".preg_quote($var, "/")."/i", $result, $url);
            preg_match ( $mask, $url, $out);
        }
        
        $url = preg_replace ("/\\\{|~%7B~/", "{" ,$url);
        $url = preg_replace ("/\\\}|~%7D~/", "}" ,$url);
        $url = html_entity_decode( $url, ENT_COMPAT, 'UTF-8' );
        
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
        $url = $this->rebuild_url_for_escaping( $url );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        $headers = array();
        $headers[] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
        $headers[] = 'Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3';
        //$headers[] = 'Accept-Encoding: gzip, deflate';
        $headers[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';// */
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

        // ua
        $ua = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);

        curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 20);

        $cookie_file = $this->getCookiesFile();
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie_file );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $cookie_file );

        if( $post_vars ) {
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_vars );
        }

        if ( preg_match( '/annoncesjaunes.fr/', $url ) ) {
            curl_setopt($ch, CURLOPT_REFERER, 'http://www.annoncesjaunes.fr/');
        }

        $data = curl_exec( $ch );
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE );
        if ( false === $data ) {
            throw new \Exception( "Erreur interne de téléchargement: " . curl_error( $ch ) );
        } elseif ( $return_code != 200 ) {
            throw new \Exception( "Erreur de téléchargement (" . $return_code . ")" );
        }
        $content_type = curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
        if(preg_match("/charset=\"?([a-z0-9-]+)\"?/si",$content_type, $matches ) ) {
            return array( $data, $matches[1] );
        } else {
            $possible_head = substr( $data, 0, 1024 );
            if ( preg_match( '/<meta +charset=["\']([^"\']+)["\']/i', $data, $matches ) ) {
                return array( $data, $matches[1] );
            } else if ( preg_match( '/<meta([^>]+content-type[^>]*)>/i', $data, $matches ) ){
                $tag = $matches[1];
                if ( preg_match( '/content=["\'][^"\']*charset=([^"\']+)["\']/i', $tag, $matches ) ) {
                    return array( $data, $matches[1] );
                }
            }
        }
        return $data;
        curl_close($ch);
    }
    private function getCookiesFile(){
        return sys_get_temp_dir() . "/cookies_" . $this->traitement->id;
    }
    
    private function clean_url($url) {
        $args = func_get_args();
        unset($args[0]);
        foreach( $args as $item ) {
            $url = preg_replace("/((?<=\?|#)|&)$item=[^&]*/si","",$url);
        }
        return $url;
    }

    public function setUrls( $full_url ) 
    {
        $this->full_url = $full_url;
        preg_match ( "/([^\?#]*)\?{0,1}([^#]*)#{0,1}(.*)/", $full_url, $match_array);
        $this->base_url = $match_array[1];
        $this->base_url_get 	= $match_array[2];
        $this->base_url_post 	= $match_array[3];
    }

    private function rebuild_url_for_escaping( $url )
    {
        return str_replace( " ", "%20", $url );
    }
    
}
