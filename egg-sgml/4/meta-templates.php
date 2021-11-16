<?php
#    meta-templates.php - Egg SGML request handler
#    Copyright 2020, 2021 Brian Jonnes

#    Egg-SGML is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, version 3 of the License.

#    Egg-SGML is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.

#    You should have received a copy of the GNU General Public License
#    along with Egg-SGML.  If not, see <https://www.gnu.org/licenses/>.

class environ {
	public $self_href, $shipyard;
	public $sct; public $scriptnow;
	public $file_ext; public $templatefile;
	public $api, $nodoctype;
	public $interimjsupgrade;
	function __construct() {
		$this->sct = [ 'br' => 1, 'hr' => 1, 'img' => 1, 'meta' => 1, 'link' => 1, 'input' => 1 ];
		$this->scriptnow = time();
	}
	function write_end_of_tag( $q, $tag ) {
		if( array_key_exists( strtolower($tag), $this->sct ) ) {
			$q->write('/>');
		} else {
			$q->write('>');
		}
	}
	function write_close_tag( $q, $tag ) {
		if( ! array_key_exists( strtolower($tag), $this->sct ) ) {
			$q->write('</' . $w->tag . '>'); }
	}
	function _untested__write_prefixed_attributes( $q, $w, $f ) {
		$m = 00;
		for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			if( substr( $w->attributes->item($m)->name, 0, 2 ) == 'a-' ) {
				$s = substr( $w->attributes->item($m)->name, 2 );
				if( array_search( $s, $f ) === false ) {
					$q->write(' ' . $s);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				}
			}
		}
	}
};

function main_f($env,$extension,$extoptional,$doc,$self_href) {
	$c = $d = $m = null; $b = 0;
	$env->self_href = $self_href;
	do {
		if( $extension != '' ) {
			if( file_exists( $doc . $extension ) ) {
				$env->templatefile = $doc . $extension;
				break; }
			if( ! $extoptional ) {
				http_response_code(404);
				return; }
		}
		if( ! file_exists( $doc ) ) {
			http_response_code(404);
			return; }			
		$env->templatefile = $doc;
	} while(0);
	$d = load_eggsgml_file_env( $env, $env->templatefile );
	$m = eggsgml_descendent( $d, 'cache_control' );
	if( $m ) if( attribute_exists($m,'static') ) {
m:		$c = apache_request_headers();
		if( array_key_exists('If-Modified-Since',$c) ) {
			$b = strtotime($c['If-Modified-Since']);
			if( filemtime($env->templatefile) <= $b ) {
				http_response_code(304);
				return; }
		}
		header('Last-Modified: ' . date('r',filemtime($env->templatefile)));
		header('Cache-Control: max-age=0');
	} else if( attribute_exists($m,'dynamic') ) {
n:		header('Last-Modified: ' . date('r',$env->scriptnow));
		header('Cache-Control: max-age=0');
	} else if( attribute_exists($m,'querystring') ) {
		if( array_key_exists( 'REDIRECT_QUERY_STRING', $_SERVER ) ) {
			goto n; }
		goto m;
	}
	$k = newframe(new tgc_generic(dirname($doc,1),$env), new echo_out, $d);
	if( ! $env->nodoctype ) {
		echo ("<!doctype html>"); }
	return $k;
}

function attribute_exists( $w, $n ) {
	$m = 00;
	if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
		if( $w->attributes->item($m)->name == $n ) return true; }
	return false;
}

function check_shipyard_auth($u) {
	if( ! file_exists($u . "/shipyard.txt") ) return true;
	$m = file_get_contents($u . '/shipyard.txt');
	if( $m === false ) return true;
	if( array_key_exists( 'shipyard', $_COOKIE ) ) {
		if( $_COOKIE['shipyard'] === $m ) {
			goto K; } }
	if( array_key_exists( 'shipyard', $_GET ) ) {
		if( $_GET['shipyard'] === $m ) {
	K:		setcookie('shipyard',$m,time()+864000);
			return true; } }
}

class tgc_templates {
	public $NF;
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$env = 00;
		if( $w->nodeName == 'main' ) {
			if( $end ) return 1;
			$path = $_SERVER['DOCUMENT_ROOT'];
			$env = new environ;
			$env->nodoctype = attribute_exists( $w, 'no-doctype' );
			$env->api = basename(dirname($_SERVER['PHP_SELF']));
			$env->shipyard = file_exists( $path . '/shipyard.txt' );
			$env->interimjsupgrade = attribute_exists( $w, 'interim-js-cdata' );
			if( $env->shipyard ) {
				$env->shipyard_auth = file_get_contents($path . '/shipyard.txt'); }
			while( attribute_exists( $w, "redirect-to-ssl" ) ) {
				if( array_key_exists('HTTPS',$_SERVER) )
					if( $_SERVER['HTTPS'] == 'on' ) break;
				header('Location:https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				return 1; 
			}
			$env->file_ext = $w->getAttribute('extension');
			if( ! check_shipyard_auth($path) ) {
				$this->NF = main_f( $env, $w->getAttribute('extension'), attribute_exists($w,'extension-optional'), $path . attribute_with_inival( $w, 'shipyard-doc', '/shipyard'), '/shipyard');
				if( ! $this->NF ) return 0;
				return 3; }
			if( $_GET['t'] == '/' ) {
				$this->NF = main_f( $env, $w->getAttribute('extension'), attribute_exists($w,'extension-optional'), $path . '/' . $w->getAttribute('rootdoc'), $_GET['t'] );
				if( ! $this->NF ) return 0;
				return 3;
			}
			if( $_GET['t'] == '/' . $w->getAttribute('rootdoc') ) {
				header('Location:https://' . $_SERVER['HTTP_HOST'] . '/' );
				return 1;
			}
			$this->NF = main_f( $env, $w->getAttribute('extension'), attribute_exists($w,'extension-optional'), $path . strtolower(str_replace('.','',$_GET['t'])), strtolower($_GET['t']) );
			if( ! $this->NF ) return 0;
			return 3;
		}
		return 0;
	}
};

function main() {
	$m = $_SERVER['DOCUMENT_ROOT'];
	if( file_exists( $m . '/templates_config.xml' ) ) {
		$d = load_eggsgml_file( $m . '/templates_config.xml' );
	} else {
		$d = load_eggsgml_file( 'templates_config.xml' );
	}
	$k = newframe(new tgc_templates, new echo_out, $d);
	$k->P = null;
	eggsgml($k);
}

main();

?>