<?php
#    tgc_test_b.php - Egg-SGML
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

class tgc_test_b__files_item {
	public $NF, $path, $env;
	public $files;
	function __construct($path,$env,$c) {
		$this->path = $path;
		$this->env = $env;
		$this->files = array_keys($c->files);
	}
	function start( $q ) {
		$this->m = 0;
		return 0; }
	function repeat( $q ) {
		$this->m += 1; return $this->m < count($this->files); }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'file_name':
			if( $end ) return 1;
			$q->write( sr_amp_lt( $this->files[$this->m] ) );
			return 2;
		}
		return 0;
	}
}

class tgc_test_b__files {
	public $NF, $path, $env;
	public $c;
	function __construct($path,$env,$c) {
		$this->path = $path;
		$this->env = $env;
		$this->c = $c;
	}
	function no_files() {
		return count($this->c->files) == 0; }
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
			if( $this->no_files() ) {
				return 1; }
			$this->NF = newframe(new tgc_test_b__files_item($this->path,$this->env,$this->c),$q,$w);
			return 3;
		}
		return 0;
	}
}

class tgc_test_b__a {
	public $NF, $path, $env;
	public $F, $U;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->files = array();
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function url( $w, $A ) {
		if( $w->getAttribute($A) == $this->U ) {
			$this->files[$this->F] = 1; }
	}
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'a':
			if( $end ) return 1;
			$this->url( $w, 'href' ); return 2;
		case 'img':
			if( $end ) return 1;
			$this->url( $w, 'src' ); return 2;
		case 'link':
			if( $end ) return 1;
			$this->url( $w, 'href' ); return 2;
		}
		return 2;
	}
}

class tgc_test_b__empty {
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

class mtgc_test_b {
	public $NF;
	public $path, $env;
	public $c, $modules;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		$n = null; $d = null; $k = null; $a = 00;
		$this->c = new tgc_test_b__a($this->path,$this->env);
		if( array_key_exists( 'U', $_GET ) ) {
			$this->c->U = $_GET['U']; }
		$n = opendir( $_SERVER['DOCUMENT_ROOT'] );
		while( ( $a = readdir($n) ) !== false ) {
			if( strtoupper( substr($a,strlen($a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
				$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $a );
				$this->c->F = $a;
				$k = newframe($this->c, new dev_null, $d);
				$k->P = null;
				eggsgml($k);
			}
		}
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$k = null;
		switch( $w->nodeName ) {
		case 'file_list':
			if( $end ) return 1;
			$k = new tgc_test_b__files($this->path,$this->env,$this->c);
			if( ! $k->no_files() ) {
				$this->NF = newframe($k,$q,$w);
			} else {
				$this->NF = newframe(new tgc_test_b__empty,$q,$w);
			}
			return 3;
		}
		return 0;
	}
}

return new mtgc_test_b;
?>