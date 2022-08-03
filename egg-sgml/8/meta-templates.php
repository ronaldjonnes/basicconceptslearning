<?php
#    meta-templates.php - Egg SGML request handler
#    Copyright 2020, 2021 Brian Jonnes

#    Egg-SGML is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, version 3 of the License.

#    Egg-SGML is distributed
#    WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.

#    You should have received a copy of the GNU General Public License
#    along with Egg-SGML.  If not, see <https://www.gnu.org/licenses/>.

class libconfig {
	public $self_href, $shipyard;
	public $sct; public $scriptnow;
	public $file_ext; public $templatefile; public $urlpath;
	public $api, $nodoctype;
	public $interimjsupgrade;
	public $secrets_path;
	function __construct() {
		$this->sct = [ 'br' => 1, 'hr' => 1, 'img' => 1, 'meta' => 1, 'link' => 1, 'input' => 1, 'base' => 1 ];
		$this->scriptnow = time();
		$this->clips = [ ];
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
			$q->write('</' . $tag . '>'); }
	}
	function expanddirpath( $H ) {
		if( $H == '' ) {
			return $_SERVER['DOCUMENT_ROOT'] . '/'; }

		if( $H[strlen($H)-1] != '/' ) $H .= '/';
		if( $H[0] == '/' ) return $H;
		return $_SERVER['DOCUMENT_ROOT'] . '/' . $H;
	}
	function dirpath2web( $P, $H ) {
		$a = $_SERVER['DOCUMENT_ROOT'];
		if( $H == '' ) {
		} else if( $H[0] == '/' ) return $H;
		if( $P == $a ) return '/' . $H;
		if( substr($P,0,strlen($a)+1) != $a . '/' ) return $P;
		$a = substr($P,strlen($a));
		if( $H != '' ) if( $a[strlen($a)-1] != '/' ) $a .= '/' . $H;
		return $a;
	}
	function load_eggsgml_file( $path ) {
		if( ! file_exists( $path ) ) {
			$this->brian( 'not found: ' . $path );
			return; }
		return load_eggsgml_file( $path );
	}
	# I'm not about to take up knitting (so I can't stick to it),
	# and I am averse to the using of medical terminology.
	# Circuit tracing belongs to electronics.
	# A debugger is an imposing tool, and in truth unless we create
	# our own picture we're not likely to 
	# increase our understanding.
	#
	# If the code belongs to us, we simply need to have more information
	# about what it's doing in order to come to understand what it's
	# doing wrong.  Some call the art of correcting others, 
	# reductio ad absurdum, but the use of Latin lends itself to 
	# the creating of (absurd) pictures of Ancient Rome, which
	# is just about the opposite reason why Latin was the language
	# of the sciences.
	#
	# Encouraging someone to talk who might've drawn a wrong conclusion
	# somewhere, who had no way of knowing it, can only be done in
	# good faith--which carries the draw-back of allowing ourselves
	# to be persuaded not to argue.
	#
	# Don't talk, just fix!
	#
	# Commanding someone to talk to us is nonsensical, yet it
	# is natural. Imagining a ship can talk to us, we would only 
	# want it to keep us informed of things that need attending to, 
	# while it is in the shipyard. Obviously.
	#
	# And obviously they call us by name.
	#
	public $shipyard_log;
	function brian( $d ) {
		if( ! $this->shipyard ) return;
		if( $this->shipyard_log != '' ) {
			$this->shipyard_log .= ' / '; }
		$this->shipyard_log .= $d;
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

class docloader_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	public $d, $end, $doc;
	function __construct($env,$extension,$altextension,$extoptional,$doc,$self_href) {
	$c = $m = null; $b = 0;
	$this->d = null; $this->env = $env; $this->doc = $doc;
	$env->self_href = $self_href;
	do {
		if( $extension != '' ) {
			if( file_exists( $doc . $extension ) ) {
				$env->templatefile = $doc . $extension;
				break; }
			if( $altextension != '' ) {
				if( file_exists( $doc . $altextension ) ) {
					$env->templatefile = $doc . $altextension;
					break; } }
			if( ! $extoptional ) {
F:				if( $env->fallback != '' ) {
					$env->templatefile = $_SERVER['DOCUMENT_ROOT'] . $env->fallback . $extension; 
					break; }
				http_response_code(404);
				return; }
		}
		if( ! file_exists( $doc ) ) {
			goto F; }
		$env->templatefile = $doc;
	} while(0);
	$this->d = load_eggsgml_file_env( $env, $env->templatefile );
	}
	function tap( $eggenv, $str ) {
		$c = $m = null; $b = 0; $k = null;
	if( ! $this->d ) return;
	$m = eggsgml_descendent( $this->d, 'cache_control' );
	if( $m ) if( attribute_exists($m,'static') ) {
m:		$c = apache_request_headers();
		if( array_key_exists('If-Modified-Since',$c) ) {
			$b = strtotime($c['If-Modified-Since']);
			if( filemtime($this->env->templatefile) <= $b ) {
				http_response_code(304);
				return; }
		}
		header('Last-Modified: ' . date('r',filemtime($this->env->templatefile)));
		header('Cache-Control: max-age=0');
	} else if( attribute_exists($m,'dynamic') ) {
n:		header('Last-Modified: ' . date('r',$this->env->scriptnow));
		header('Cache-Control: max-age=0');
	} else if( attribute_exists($m,'querystring') ) {
		if( array_key_exists( 'REDIRECT_QUERY_STRING', $_SERVER ) ) {
			goto n; }
		goto m;
	}
	enqueue_modules( $eggenv, $this->doc, $this->env );
	$k = new tag_egg( $this->d, $this->doc );
	//domegg;
	//$k->tgcnode = $eggenv->stack;
	//$k->writernode = ( $eggenv->stack->q ? $eggenv->stack : $eggenv->stack->writernode );
	//$k->dn = $this->d;
	$eggenv->enqueue_surreal( $k, [ ] );

	//$k = newframe(new tgc_generic(dirname($doc,1),$env), new echo_out, $d);
	//if( ! $env->nodoctype ) {
	//	echo ("<!doctype html>"); }
	//return $k;
}
};

function attribute_exists( $w, $n ) {
	$m = 00;
	if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
		if( $w->attributes->item($m)->name == $n ) return true; }
	return false;
}

function check_shipyard_auth($env,$u) {
	if( ! $env->shipyard ) return true;
	$m = $env->shipyard_auth;
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
	public $lc;
	function __construct($lc) {
		$this->lc = $lc;
	}
	function self_protocol($w) {
		if( attribute_exists( $w, "redirect-to-ssl" ) || array_key_exists('HTTPS',$_SERVER) ) {
			return 'https'; }
		return 'http';
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$env = null;
		if( $w->nodeName == 'main' ) {
			if( $end ) return 1;
			$path = $_SERVER['DOCUMENT_ROOT'];
			$env = $this->lc;
			$env->secrets_path = $env->expanddirpath( $w->getAttribute('secrets-path') );
			$env->urlpath = $_GET['t'];
			$env->nodoctype = true;
			$env->api = basename(dirname($_SERVER['PHP_SELF']));
			$env->interimjsupgrade = true;
			if( file_exists( $path . '/shipyard.txt' ) ) {
				if( attribute_exists( $w, 'shipyard-auth' ) ) {
					$env->shipyard_auth = $w->getAttribute('shipyard-auth');
				} else {
					$env->shipyard_auth = file_get_contents($path . '/shipyard.txt');
				}
				$env->shipyard = true; }
			$env->fallback = $w->getAttribute('fallback');
			while( attribute_exists( $w, "redirect-to-ssl" ) ) {
				if( array_key_exists('HTTPS',$_SERVER) )
					if( $_SERVER['HTTPS'] == 'on' ) break;
				header('Location:https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				return 1; 
			}
			$env->file_ext = $w->getAttribute('extension');
			if( ! check_shipyard_auth($env,$path) ) {
				$this->NF = new docloader_egg( $env, $w->getAttribute('extension'), $w->getAttribute('alt-extension'), attribute_exists($w,'extension-optional'), $path . attribute_with_inival( $w, 'shipyard-doc', '/shipyard'), '/shipyard');
				//if( ! $this->NF ) return 1;
				return 4; }
			if( $_GET['t'] == '/' ) {
				$this->NF = new docloader_egg( $env, $w->getAttribute('extension'), $w->getAttribute('alt-extension'), attribute_exists($w,'extension-optional'), $path . '/' . $w->getAttribute('rootdoc'), $_GET['t'] );
				//if( ! $this->NF ) return 1;
				return 4;
			}
			if( $_GET['t'] == '/' . $w->getAttribute('rootdoc') ) {
				header('Location:' . $this->self_protocol($w) . '://' . $_SERVER['HTTP_HOST'] . '/' );
				return 1;
			}
			$this->NF = new docloader_egg( $env, $w->getAttribute('extension'), $w->getAttribute('alt-extension'), attribute_exists($w,'extension-optional'), $path . strtolower(str_replace('.','',$_GET['t'])), strtolower($_GET['t']) );
			//if( ! $this->NF ) return 1;
			return 4;
		}
		return 0;
	}
};

function main() {
	$m = 00; $k = $d = $lc = null;
	$m = $_SERVER['DOCUMENT_ROOT'];
	if( file_exists( $m . '/templates_config.xml' ) ) {
		$d = load_eggsgml_file( $m . '/templates_config.xml' );
	} else {
		$d = load_eggsgml_file( 'templates_config.xml' );
	}
	$lc = new libconfig;
	$k = newframe(new tgc_templates($lc), new echo_out, $d);
	$k->P = null;
	eggsgml($lc,$k,'');
}

main();

?>
