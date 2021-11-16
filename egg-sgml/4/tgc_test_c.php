<?php
#    tgc_test_c.php - Egg-SGML
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

class tgc_test_c__empty {
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
		if( $this->inner && $w === $this->w ) {
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

class tgc_test_c__image_b {
	public $NF, $path, $env;
	function __construct($path,$env,$v) {
		$this->path = $path;
		$this->env = $env;
		$this->v = $v;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'src':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->v->getAttribute('src') ) );
			return 2;
		case 'alt':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->v->getAttribute('alt') ) );
			return 2;
		}
		if( $end ) {
			$q->write('</' . $w->nodeName . '>');
			return 1; }
		$q->write('<' . $w->nodeName );
		write_attributes( $q, $w, [ ] );
		$q->write('>');
		return 2;
	}
};

class tgc_test_c__image_c {
	public $NF, $path, $env;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		return 0; }
};

class tgc_test_c__image {
	public $NF, $path, $env;
	function __construct($path,$env,$v) {
		$this->path = $path;
		$this->env = $env;
		$this->v = $v;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		$m = null;
		switch( $w->nodeName ) {
		case 'img':
			if( $end ) return 1;
			$this->NF = newframe( new tgc_test_c__image_b($this->path,$this->env,$w), $q, $this->v );
			return 3;
		case 'include':
			if ($end) return 1;
			$m = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $w->getAttribute('path') );
			$this->NF = newframe(new tgc_test_c__image_c($this->path,$this->env),$q,$m);
			return 3;
		}
		if( $end ) return 1;
		return 2;
	}
};

class tgc_test_c__a {
	public $NF, $path, $env;
	public $clipname;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->clipname = $clipname;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'record':
			if( $end ) return 1;
			if( $w->getAttribute('id') == $this->clipname ) {
				$this->NF = newframe( new tgc_sgml_source(''), $q, $w );
				return 3; }
			return 1;
		}
		if( $end ) return 1;
		return 2;
	}
};

class tgc_test_c__page {
	public $NF, $path, $env;
	public $n, $a;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->n = opendir( $_SERVER['DOCUMENT_ROOT'] );
		$this->c = new tgc_test_c__a($path,$env,$clipname);
	}
	function read() {
		while(1) {
			$this->a = readdir($this->n);
			if( $this->a === false ) {
				return false; }
			if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
				break; }
		}
		return true;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		if( $this->read() ) {
			return 1; }
		return 0;
	}
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$d = null;
		switch($w->nodeName) {
		case 'clip_value':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$this->NF = newframe($this->c, $q, $d);
			return 3;
		case 'image':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$this->NF = newframe( new tgc_test_c__image($this->path,$this->env,$w), $q, $d );
			return 3;
		case 'cache_control_value':
			if( $end ) return 1;
			$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
			$d = eggsgml_descendent( $d, 'cache_control' );
			if( $d ) {
				if( attribute_exists( $d, 'static' ) ) {
					$q->write('static');
				} else if( attribute_exists( $d, 'dynamic' ) ) {
					$q->write('dynamic');
				} else if( attribute_exists( $d, 'querystring' ) ) {
					$q->write('querystring');
				}
			}
			return 2;
		case 'page_name':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->a ) );
			return 2;
		}
		return 0;
	}
};

class tgc_test_c__pages {
	public $NF, $path, $env;
	public $m;
	function __construct($path,$env,$clipname) {
		$this->path = $path;
		$this->env = $env;
		$this->m = new tgc_test_c__page($this->path,$this->env,$clipname);
	}
	function open() {
		return $this->m->read();
//		$this->n = opendir( $_SERVER['DOCUMENT_ROOT'] );
//		while(1) {
//			$this->a = readdir($this->n);
//			if( $this->a === false ) {
//				return false; }
//			if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
//				return true; }
//		}
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'empty':
			return 1;
		case 'item':
			if( $end ) return 1;
			$this->NF = newframe( $this->m, $q, $w );
			return 3;
		}
		return 0;
	}
};

class mtgc_test_c {
	public $NF;
	public $path, $env;
	public $clipname;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
		if( array_key_exists('clip',$_GET) ) {
			$this->clipname = $_GET['clip']; }
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$c = null;
		switch($w->nodeName) {
		case 'pages':
			if( $end ) return 1;
			$c = new tgc_test_c__pages($this->path,$this->env,$this->clipname);
			if( $c->open() ) {
				$this->NF = newframe( $c, $q, $w );
			} else {
				$this->NF = newframe( new tgc_test_c__empty, $q, $w );
			}
			return 3;
		}
		return 0;
	}
}

return new mtgc_test_c;
?>