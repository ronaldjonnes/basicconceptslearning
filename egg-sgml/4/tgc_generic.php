<?php
#    tgc_generic.php - Egg SGML
#    Copyright (C) 2020 Brian Jonnes

#    This library is free software; you can redistribute it and/or
#    modify it under the terms of the GNU Lesser General Public
#    License as published by the Free Software Foundation; either
#    version 2.1 of the License, or (at your option) any later version.

#    This library is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
#    Lesser General Public License for more details.

#    You should have received a copy of the GNU Lesser General Public
#    License along with this library; if not, write to the Free Software
#    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

class tgc_sgml_source {
	public $NF;
	public $hlight;
	function __construct($hlight) {
		$this->hlight = explode(' ', $hlight);
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&amp;lt;", str_replace( "&", "&amp;amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = false;
		if( $end ) {
			if( $w->firstChild != null ) {
				if( ! ( array_search($w->nodeName,$this->hlight) === false ) )  {
					$q->write('<b>'); $a = true; }
				$q->write('&lt;/<u>' . $w->nodeName . '</u>>');
				if( $a ) $q->write('</b>'); 
			}
			return 1; }
		if( ! ( array_search($w->nodeName,$this->hlight) === false ) )  {
			$q->write('<b>'); $a = true; }
		$q->write('&lt;<u>' . $w->nodeName . '</u>');
		for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
		if( $w->firstChild == null ) {
			$q->write('/'); }
		$q->write('>');
		if( $a ) $q->write('</b>'); 
		return 2; }
}

function strmerge( $a, $b, $c ) {
	if( $a != '' ) {
		if( $b != '' ) {
			return $a . $c . $b;
		} return $a;
	} return $b;
}

class tgc_module {
	public $NF;
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&amp;lt;", str_replace( "&", "&amp;amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( $end ) return 1;
		if( $w->nodeName == 'module' ) {
			return 3;
		}
		return 0;
	}
};

function load_module_frame( $path, $env, $w, $q ) {
	$a = include $path . '/' . $w->getAttribute('path');
	$a->initialize( $path, $env );
	return newframe( $a, $q, $w);
}

function load_module_frame_api( $path, $env, $w, $q ) {
	$a = include $_SERVER['DOCUMENT_ROOT'] . '/' . $env->api . '/' . $w->getAttribute('path');
	$a->initialize( $path, $env );
	return newframe( $a, $q, $w );
}

class tgc_generic {
	public $path, $env;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->clips = [ ];
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$m = $f = $a = 00;
		# local $m, $f
		if( $w->nodeName == 'cache_control' ) {
			return 1; }
		if( $w->nodeName == 'form.cache_dynamic' ) {
			if( $end ) {
				$q->write('</form>'); return 1; }
			$q->write('<form');
			write_attributes( $q, $w, array('action') );
			if( $w->getAttribute('method') == 'post' ) {
				$q->write(' action="' . sr_amp_quot( $_SERVER['REDIRECT_URL'] ) . '?' . rand(0,100) . '"' ); }
			$q->write('>');
			return 2; }
		if( $w->nodeName == 'doctype' ) {
			if( $end ) return 1;
			$q->write('<!' . $w->getAttribute('raw') . '>');
			return 2; }
		if( $w->nodeName == 'tag' ) {
			return 2; }
		if( $w->nodeName == 'sailing' ) {
			if( $end ) return 1;
			if( $this->env->shipyard ) return 1;
			return 2; }
		if( $w->nodeName == 'shipyard' ) {
			if( $end ) return 1;
			if( $this->env->shipyard ) return 2;
			return 1; }
		if( $w->nodeName == 'eggsgml_version' ) {
			if( $end ) return 1;
			$q->write( sr_amp_lt( eggsgml_version() ) );
			return 2; }
		if( $w->nodeName == 'eggsgml_api_version' ) {
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->env->api ) );
			return 2; }
		if( $w->nodeName == 'a.site' ) {
			if( $end ) {
				$q->write('</a>');
				return 1; }
			$q->write('<a');
			if( $w->getAttribute('href') == $this->env->self_href ) {
				$f = strmerge( $w->getAttribute('activeclass'), $w->getAttribute('class'), ' ' );
			} else {
				$f = $w->getAttribute('class');
			}
			if( $f != '' ) {
				$q->write(' class="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $f ) ) . '"' );
			}
			for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				if( $w->attributes->item($m)->name == 'activeclass' ) {
				} else {
					$q->write(' ' . $w->attributes->item($m)->name);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				}
			}
			$q->write('>');
			return 2; }
		if( $w->nodeName == 'module' ) {
			if ($end) return 1;
			if( attribute_exists( $w, 'api' ) ) {
				$this->NF = load_module_frame_api($this->path,$this->env,$w,$q);
			} else {
				$this->NF = load_module_frame($this->path,$this->env,$w,$q);
			}
			return 3; }
		if( $w->nodeName == 'include' ) {
			if ($end) return 1;
			$a = $this->path . "/" . $w->getAttribute('path');
			$m = load_eggsgml_file_env( $this->env, $a );
			if( dirname($a,1) != $this->path ) {
				$this->NF = newframe( new tgc_generic(dirname($a,1),$this->env), $q, $m );
			} else {
				$this->NF = newframe(new tgc,$q,$m);
			}
			return 3; }
		if( $w->nodeName == 'showsource' ) {
			if ($end) return 1;
			$m = load_eggsgml_file_env( $this->env, $this->path . "/" . $w->getAttribute('path') );
			$this->NF = newframe(new tgc_sgml_source($w->getAttribute('highlight')),$q,$m);
			return 3; }
		if( $w->nodeName == 'showphp' ) {
			if ($end) return 1;
			$m = file_get_contents($this->path . "/" . $w->getAttribute('path'));
			$q->write(str_replace("\n", "<br/>", str_replace("\t", "&nbsp; &nbsp; &nbsp; ", str_replace("<","&lt;", str_replace( "&", "&amp;", $m ) ) ) ) );
			return 2; }
		if( $w->nodeName == 'redirect' ) {
			if ($end) return 1;
			header('Location:' . $w->getAttribute('location'));
			return 1; }
		if( $w->nodeName == 'record' ) {
			if ($end) return 1;
			$this->clips[$w->getAttribute('id')] = $w;
			return 1; }
		if( $w->nodeName == 'play' ) {
			if ($end) return 1;
			if( array_key_exists( $w->getAttribute('id'), $this->clips ) ) {
				$this->NF = newframe(new tgc,$q,$this->clips[$w->getAttribute('id')]);
				return 3; }
			return 1; }
		if( $w->nodeName == 'servervariable' ) {
			if ($end) return 1;
			$q->write(str_replace('<','&lt;',str_replace('&','&amp;',$_SERVER[$w->getAttribute('name')])));
			return 1; }
		if( $w->nodeName == 'shipyard_login_failed' ) {
			if ($end) return 1;
			if( array_key_exists( 'shipyard', $_GET ) ) return 2;
			return 1; }
		if( $w->nodeName == 'script' ) {
			if ($end) return 1;
			$q->write('<' . $w->nodeName);
			for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				$q->write(' ' . $w->attributes->item($m)->name);
				if( $w->attributes->item($m)->value != null ) {
					$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
			}
			$q->write('>');
			$w1 = $w->firstChild;
			while( $w1 != null ) {
				$q->write( $w1->data ); $w1 = $w1->nextSibling; }
			$q->write('</script>');
			return 1; }
		if( $end ) {
			if( $w->firstChild != null && ! array_key_exists( strtolower($w->nodeName), $this->env->sct ) ) {
				$q->write('</' . $w->nodeName . '>'); }
			return 1; }
		$q->write('<' . $w->nodeName);
		if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
		if( $w->firstChild == null ) {
			if( array_key_exists( strtolower($w->nodeName), $this->env->sct ) ) {
				$q->write('/>');
			} else {
				$q->write('></' . $w->nodeName . '>');
			}
		} else {
			$q->write('>'); }
		return 2; }
}
?>