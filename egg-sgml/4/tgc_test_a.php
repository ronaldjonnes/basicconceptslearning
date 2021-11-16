<?php
#    tgc_test_a.php - Egg-SGML
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

function _sr_25( $x, $k ) {
	$d = 00; $n = '';
	for( $d = 0; $d < strlen($x); $d += 1 ) {
		if( $x[$d] == '%' || strpos($k,$x[$d]) !== false ) {
			$n .= '%' . str_pad( dechex(ord($x[$d])), 2, '0', STR_PAD_LEFT );
		} else $n .= $x[$d];
	}
	return $n;
}

class null_buffer2 {
	function write($m) { }
};

class mtgc_module_test__modules {
	public $a;
	function __construct() {
		$this->a = array();
	}
};

class mtgc_module_test__urls {
	public $a;
	function __construct() {
		$this->a = array();
	}
};

class mtgc_module_test__list {
	public $a;
	function __construct() {
		$this->a = array();
	}
};

class tgc_module_test__empty {
	public $NF;
	public $inner, $w;
	function start( $q ) {
		$this->inner = false;
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		if( $this->inner ) {
			$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	}
	function consume( $q, $end, $w ) {
		if( $this->inner && $w == $this->w ) {
			// $end 
			$this->inner = false;
			return 1; }
		if( $w->nodeName == 'empty' ) {
			if( $this->inner )
				return 0;
			if( $end ) {
				// ! $w == $this->w
				return 1; }
			$this->inner = true;
			$this->w = $w;
			return 2;
		}
		if( $this->inner ) 
			return 0;
		return 2;
	}
};

class tgc_module_test__a {
	public $NF;
	public $path, $env;
	public $testpath;
	public $modules, $urls;
	public $clips;
	function __construct($path,$env,$testpath,$modules,$urls,$tags) {
		$this->path = $path;
		$this->env = $env;
		$this->testpath = $testpath;
		$this->modules = $modules;
		$this->urls = $urls;
		$this->clips = new mtgc_module_test__list;
		$this->tags = $tags;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( array_key_exists( $w->nodeName, $this->tags->a ) )
			$this->tags->a[$w->nodeName] += 1;
		else
			$this->tags->a[$w->nodeName] = 1;

		if( $w->nodeName == 'module' ) {
			if( $end ) return 1;
			if( ! array_key_exists( $w->getAttribute('path'), $this->modules->a ) ) {
				$this->modules->a[$w->getAttribute('path')] = 1;
				if( attribute_exists($w,'api') ) {
					$a = include $_SERVER['DOCUMENT_ROOT'] . '/' . $this->env->api . '/' . $w->getAttribute('path');
				} else {
					$a = include $this->testpath . '/' . $w->getAttribute('path');
				}
			}
			return 2; }
		if( $w->nodeName == 'a' ) {
			if( $end ) return 1;
			$this->urls->a[$w->getAttribute('href')]['a'] = 1;
			return 2; }
		if( $w->nodeName == 'link' ) {
			if( $end ) return 1;
			$this->urls->a[$w->getAttribute('href')]['link'] = 1;
			return 2; }
		if( $w->nodeName == 'img' ) {
			if( $end ) return 1;
			$this->urls->a[$w->getAttribute('src')]['img'] = 1;
			return 2; }
		if( $w->nodeName == 'record' ) {
			if( $end ) return 1;
			$this->clips->a[$w->getAttribute('id')]['record'] = 1;
			return 2; }
		if( $w->nodeName == 'play' ) {
			if( $end ) return 1;
			$this->clips->a[$w->getAttribute('id')]['play'] = 1;
			return 2; }
		if( $w->nodeName == 'include' ) {
			if ($end) return 1;
			$a = $this->testpath . "/" . $w->getAttribute('path');
			$m = load_eggsgml_file( $a );
			if( dirname($a,1) != $this->testpath ) {
				$this->NF = newframe( new tgc_module_test__a($this->path,$this->env,dirname($a,1),$this->modules), $q, $m );
			} else {
				$this->NF = newframe(new tgc,$q,$m);
			}
			return 3; }
		return 2;
	}
};

class tgc_module_test__modules_item {
	public $NF;
	public $path, $env;
	public $modules;
	function __construct($path,$env,$modules) {
		$this->path = $path;
		$this->env = $env;
		$this->modules = array_keys($modules->a);
	}
	function start( $q ) {
		$this->m = 0;
		return 0; }
	function repeat( $q ) {
		$this->m += 1;
		return $this->m < count($this->modules); }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( $w->nodeName == 'module_name' ) {
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->modules[$this->m] ) );
			return 1; }
		return 0;
	}
};

class tgc_module_test__modules {
	public $NF;
	public $path, $env;
	public $modules;
	function __construct($path,$env,$modules) {
		$this->path = $path;
		$this->env = $env;
		$this->modules = $modules;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = 00; $d = $k = $n = null;
		if( $w->nodeName == 'item' ) {
			$this->NF = newframe(new tgc_module_test__modules_item($this->path,$this->env,$this->modules),$q,$w);
			return 3;
		}
		return 0;
	}
};

class tgc_module_test__url_item {
	public $NF;
	public $path, $env;
	public $urls;
	function __construct($path,$env,$urls) {
		$this->path = $path;
		$this->env = $env;
		$this->urls = array_keys($urls->a);
	}
	function start( $q ) {
		$this->m = 0;
		return 0; }
	function repeat( $q ) {
		$this->m += 1;
		return $this->m < count($this->urls); }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'url_value':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->urls[$this->m] ) );
			return 1;
		case 'a.url':
			if( $end ) {
				$q->write('</a>'); return 1; }
			$q->write('<a');
			write_attributes( $q, $w, array('href') );
			$q->write(' href="/tests/test_2b?U=' . _sr_25( $this->urls[$this->m], '&#+ ' ) . '"' );
			$q->write('>');
			return 2;
		}
		return 0;
	}
};

class tgc_module_test__urls {
	public $NF;
	public $path, $env;
	public $urls;
	function __construct($path,$env,$urls) {
		$this->path = $path;
		$this->env = $env;
		$this->urls = $urls;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = 00; $d = $k = $n = null;
		if( $w->nodeName == 'item' ) {
			$this->NF = newframe(new tgc_module_test__url_item($this->path,$this->env,$this->urls),$q,$w);
			return 3;
		}
		return 0;
	}
};

class tgc_module_test__tag_item {
	public $NF;
	public $path, $env;
	public $tags, $_tags;
	function __construct($path,$env,$tags) {
		$this->path = $path;
		$this->env = $env;
		$this->_tags = $tags->a;
		$this->tags = array_keys($tags->a);
	}
	function start( $q ) {
		$this->m = 0;
		return 0; }
	function repeat( $q ) {
		$this->m += 1;
		return $this->m < count($this->tags); }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'tag_name':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->tags[$this->m] ) );
			return 1;
		case 'tally':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->_tags[ $this->tags[$this->m] ] ) );
/*		case 'a.url':
			if( $end ) {
				$q->write('</a>'); return 1; }
			$q->write('<a');
			write_attributes( $q, $w, array('href') );
			$q->write(' href="/tests/test_2b?U=' . _sr_25( $this->urls[$this->m], '&#+ ' ) . '"' );
			$q->write('>');
			return 2; */
		}
		return 0;
	}
};

class tgc_module_test__tags {
	public $NF;
	public $path, $env;
	public $tags;
	function __construct($path,$env,$tags) {
		$this->path = $path;
		$this->env = $env;
		$this->tags = $tags;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = 00; $d = $k = $n = null;
		if( $w->nodeName == 'item' ) {
			$this->NF = newframe(new tgc_module_test__tag_item($this->path,$this->env,$this->tags),$q,$w);
			return 3;
		}
		return 0;
	}
};

class tgc_module_test__clips_item {
	public $NF;
	public $path, $env;
	public $clips;
	function __construct($path,$env,$clips) {
		$this->path = $path;
		$this->env = $env;
		$this->clips = array_keys($clips->a);
	}
	function start( $q ) {
		$this->m = 0;
		return 0; }
	function repeat( $q ) {
		$this->m += 1;
		return $this->m < count($this->clips); }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch($w->nodeName) {
		case 'clip_name':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->clips[$this->m] ) );
			return 1;
		case 'a.clip':
			if( $end ) {
				$q->write('</a>'); return 1; }
			$q->write('<a');
			write_attributes( $q, $w, array('href') );
			$q->write(' href="/tests/test_3b?clip=' . _sr_25( $this->clips[$this->m], '&#+ ' ) . '"' );
			$q->write('>');
			return 2;
		}
		return 0;
	}
};

class tgc_module_test__clips {
	public $NF;
	public $path, $env;
	public $clips;
	function __construct($path,$env,$clips) {
		$this->path = $path;
		$this->env = $env;
		$this->clips = $clips;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = 00; $d = $k = $n = null;
		if( $w->nodeName == 'item' ) {
			$this->NF = newframe(new tgc_module_test__clips_item($this->path,$this->env,$this->clips),$q,$w);
			return 3;
		}
		return 0;
	}
};

class tgc_module_test__files_item {
	public $NF;
	public $path, $env;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->n = opendir($_SERVER['DOCUMENT_ROOT']);
		$this->a = "";
	}
	function read() {
		while( ( $this->a = readdir($this->n) ) !== false )
				if( strtoupper( substr($this->a,strlen($this->a)-4) ) == strtoupper( $this->env->file_ext ) ) {
					return true; }
		return false;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return $this->read(); }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( $w->nodeName == 'file_name' ) {
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->a ) );
			return 1; }
		return 0;
	}
};

class tgc_module_test__files {
	public $NF;
	public $path, $env;
	public $no_files;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->y = new tgc_module_test__files_item($this->path,$this->env);
		$this->no_files = ! $this->y->read();
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = 00; $d = $k = $n = null;
		if( $w->nodeName == 'empty' ) {
			return 1; }
		if( $w->nodeName == 'item' ) {
			if( $end ) return 1;
			if( $this->no_files ) {
				return 1; }
			$this->NF = newframe($this->y,$q,$w);
			return 3;
		}
		return 0;
	}
};

class mtgc_module_test {
	public $NF;
	public $path, $env;
	public $c, $modules, $tags;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->modules = new mtgc_module_test__modules;
		$this->urls = new mtgc_module_test__urls;
		$this->tags = new mtgc_module_test__list;
		$this->c = new tgc_module_test__a($path,$env,$_SERVER['DOCUMENT_ROOT'],$this->modules,$this->urls,$this->tags);
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = 00; $d = $k = $n = null;
		switch( $w->nodeName ) {
		case 'module_list':
			if( $end ) return 1;
			$n = opendir( $_SERVER['DOCUMENT_ROOT'] );
			while( ( $a = readdir($n) ) !== false ) {
				if( strtoupper( substr($a,strlen($a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
					$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $a );
					$k = newframe($this->c, new null_buffer2, $d);
					$k->P = null;
					eggsgml($k);
				}
			}
			$this->NF = newframe(new tgc_module_test__modules($this->path,$this->env,$this->modules),$q,$w);
			return 3;
		case 'url_list':
			if( $end ) return 1;
			$this->NF = newframe( new tgc_module_test__urls($this->path,$this->env,$this->urls),$q,$w);
			return 3;
		case 'tag_list':
			if( $end ) return 1;
			$this->NF = newframe( new tgc_module_test__tags($this->path,$this->env,$this->tags),$q,$w);
			return 3;
		case 'file_list':
			if( $end ) return 1;
			$k = new tgc_module_test__files($this->path,$this->env);
			if( ! $k->no_files ) {
				$this->NF = newframe($k,$q,$w);
			} else {
				$this->NF = newframe(new tgc_module_test__empty,$q,$w);
			}
			return 3;
		case 'clips_list':
			if( $end ) return 1;
			$this->NF = newframe( new tgc_module_test__clips($this->path,$this->env,$this->c->clips),$q,$w);
			return 3;
		}
		return 0;
	}
}

return new mtgc_module_test;

?>