<?php
#    tgc_generic.php - Egg SGML
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

/* This is dedicated to the C-Drive Internet Cafe. */
class tgc_generic {
	public $path, $env;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$m = $c = $f = $a = 00;
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
		if( $w->nodeName == 'shipyard-log' ) {
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->env->shipyard_log ) );
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
			$c = eggsgml_version();
			if( attribute_exists( $w, 'decimal' ) )
				$c = str_replace( '.', $w->getAttribute('decimal'), $c );
			$q->write( sr_amp_lt( $c ) );
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
			$c = $w->getAttribute('href');
			$c = $this->env->dirpath2web($this->path,$c); 
			if( $c == $this->env->self_href ) {
				$f = strmerge( $w->getAttribute('activeclass'), $w->getAttribute('class'), ' ' );
			} else {
				$f = $w->getAttribute('class');
			}
			if( $f != '' ) {
				$q->write(' class="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $f ) ) . '"' );
			}
			write_attributes( $q, $w, [ 'class', 'activeclass', 'href' ] );
			$q->write(' href="' . sr_amp_quot( $c ) . '"' );
		//	for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
		//		if( $w->attributes->item($m)->name == 'activeclass' ) {
		//		} else {
		//			$q->write(' ' . $w->attributes->item($m)->name);
		//			if( $w->attributes->item($m)->value != null ) {
		//				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		//		}
		//	}
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
			$a = $this->path;
			if( strlen($a) > 0 ) if( $a[strlen($a)-1] != '/' ) $a .= '/';
			$b = $w->getAttribute('path');
			if( strlen($b) > 0 ) if( $b[0] == '/' ) $b = substr($b,1);
			$a .= $b;
			$m = load_eggsgml_file_env( $this->env, $a );
			if( !attribute_exists($w,'interim-no-relative') && dirname($a,1) != $this->path ) {
				$this->NF = newframe( new tgc_generic_but(dirname($a,1),$this->env), $q, $m );
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
			$this->env->clips[$w->getAttribute('id')] = $w;
			$this->env->clip_path[$w->getAttribute('id')] = $this->path;
			return 1; }
		if( $w->nodeName == 'play' ) {
			if ($end) return 1;
			if( array_key_exists( $w->getAttribute('id'), $this->env->clips ) ) {
				$this->NF = newframe(new tgc_generic_but($this->env->clip_path[$w->getAttribute('id')],$this->env),$q,$this->env->clips[$w->getAttribute('id')]);
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
			$this->env->write_close_tag($q,$w->nodeName);
			return 1; }
		$q->write('<' . $w->nodeName);
		if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
		$this->env->write_end_of_tag($q,$w->nodeName);
		return 2; }
}
/* This is dedicated to a person who was at the awkward age which makes us uncertain whether to refer
 * to her as a girl or a woman, who didn't have enough sense to tell a boy who had intended to grow his 
 * hair long but found it always in his face, to learn to tie a shoelace behind his head. */
class tgc_generic_excuse_me {
	public $path, $env;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( $w->nodeName == 'play' ) {
			if ($end) return 1;
			if( array_key_exists( $w->getAttribute('id'), $this->env->clips ) ) {
				$this->NF = newframe(new tgc_generic_but($this->env->clip_path[$w->getAttribute('id')],$this->env),$q,$this->env->clips[$w->getAttribute('id')]);
				return 3; }
			return 1; }
		return 0; }
}

class tgc_generic_but {
	public $path, $env;
	public $NF;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$m = $c = $f = $a = 00;
		# local $m, $f
		if( $w->nodeName == 'a.site' ) {
			if( $end ) {
				$q->write('</a>');
				return 1; }
			$q->write('<a');
			$c = $w->getAttribute('href');
			$c = $this->env->dirpath2web($this->path,$c); 
			if( $c == $this->env->self_href ) {
				$f = strmerge( $w->getAttribute('activeclass'), $w->getAttribute('class'), ' ' );
			} else {
				$f = $w->getAttribute('class');
			}
			if( $f != '' ) {
				$q->write(' class="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $f ) ) . '"' );
			}
			write_attributes( $q, $w, [ 'class', 'activeclass', 'href' ] );
			$q->write(' href="' . sr_amp_quot( $c ) . '"' );
		//	for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
		//		if( $w->attributes->item($m)->name == 'activeclass' ) {
		//		} else {
		//			$q->write(' ' . $w->attributes->item($m)->name);
		//			if( $w->attributes->item($m)->value != null ) {
		//				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		//		}
		//	}
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
			$a = $this->path;
			if( strlen($a) > 0 ) if( $a[strlen($a)-1] != '/' ) $a .= '/';
			$b = $w->getAttribute('path');
			if( strlen($b) > 0 ) if( $b[0] == '/' ) $b = substr($b,1);
			$a .= $b;
			$m = load_eggsgml_file_env( $this->env, $a );
			if( !attribute_exists($w,'interim-no-relative') && dirname($a,1) != $this->path ) {
				$this->NF = newframe( new tgc_generic_but(dirname($a,1),$this->env), $q, $m );
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
		if( $w->nodeName == 'record' ) {
			if ($end) return 1;
			$this->env->clips[$w->getAttribute('id')] = $w;
			$this->env->clip_path[$w->getAttribute('id')] = $this->path;
			return 1; }
		if( $w->nodeName == 'play' ) {
			if ($end) return 1;
			if( array_key_exists( $w->getAttribute('id'), $this->env->clips ) ) {
				$this->NF = newframe(new tgc_generic_but($this->env->clip_path[$w->getAttribute('id')],$this->env),$q,$this->env->clips[$w->getAttribute('id')]);
				return 3; }
			return 1; }
		return 0; }
}
?>
