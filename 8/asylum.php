<?php
#    asylum.php - Egg-SGML
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

/*
function apply_relative_path( $m, $n ) {
.
	while(1) {
		$d = $h;
		$h = strpos( $d, $n, '/' );
		if( $h == -1 ) {
. }
		if( substr( $n, $d, $h - $d ) == '.' ) {
			$k = strrpos( $m, '/' );
			if( $k == -1 ) {
.return ''; }
			if( $k == 0 ) {
.
				return '/'; }
			$m = substr( $m, 0, $k + 1 );
		} else if( substr( $n, $d, $h - $d ) == '..' ) {
			$k = strrpos( $m, '/' );
			if( $k == -1 ) {
. }
			if( $k == 0 ) {
.
				return '/'; }
			$k = strrpos( $k - 1, $m, '/' );
			if( $k == -1 ) {
. }
			if( $k == 0 ) {
				.
			}
			$m = substr( $m, 0, $k );
		}
*/

class asylum_config {
	public $path;
	public $hrefs;
	function __construct() {
		$this->path = $_SERVER['DOCUMENT_ROOT'];
		$this->hrefs = [ ];
	}
};

class tgctestc_dictionary {
	public $tsq, $tsr, $tsw;
	public $writernode;
	
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		$env->write( sr_amp_lt( $env->diag_diplomats() ) );
	}
};

class tgctestc_pagename {
	public $tsq, $tsr, $tsw, $tsidents;
	
	function __construct( $a ) {
		$this->a = $a;
	}
	function tap( $env, $tsr ) {
		$env->write( sr_amp_lt( $this->a ) );
	}
};
class tgctestc_cachecontrol {
	public $tsq, $tsr, $tsw, $tsidents;
	
	function __construct( $a ) {
		$this->a = $a;
	}
	function tap( $env, $tsr ) {
		$d = load_eggsgml_file( $_SERVER['DOCUMENT_ROOT'] . '/' . $this->a );
		$d = eggsgml_descendent( $d, 'cache_control' );
		if( $d ) {
			if( attribute_exists( $d, 'static' ) ) {
				$env->write('static');
			} else if( attribute_exists( $d, 'dynamic' ) ) {
				$env->write('dynamic');
			} else if( attribute_exists( $d, 'querystring' ) ) {
				$env->write('querystring');
			}
		}
	}
};

class subtag_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	public $dn, $dnpath;
	function __construct($dn, $dnpath) {
		$this->dn = $dn;
		$this->dnpath = $dnpath; }
	function tap( $env, $str ) {
		sane_enqueue_subtree( $env, $this->dn, $this->dnpath );
	}
};

class href_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	public $dn, $dnpath, $p;
	function __construct($dn, $dnpath, $p) {
		$this->dn = $dn;
		$this->dnpath = $dnpath;
		$this->p = $p; }
	function tap( $env, $str ) {
		if( $str ) {
			$env->write('</a>');
			return; }
		$env->write('<a href="' . sr_amp_quot($this->p) . '"');
		write_attributes( $env, $this->dn, [ 'href' ] );
		$env->write('>');
		$env->enqueue_surreal( $this, [ ] );
		sane_enqueue_subtree( $env, $this->dn, $this->dnpath );
	}
};

class onion_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	public $dn, $dnpath;
	public $title, $feature, $pith, $date;
	public $clips, $clip_path;
	function __construct() {
		$this->tgc = $this; 
		$this->clips = [ ];
		$this->clip_path = [ ];
	}

	function write( $a ) { }
	function tap( $env, $str ) {
		if( $str ) {
			return; }
		$env->enqueue_perfectbliss( $this, [ 'red-atom:record', 'tgc', 'earring' ] );
		sane_enqueue_subtree( $env, $this->dn, $this->dnpath );
	}
	function consume_text( $q, $x ) { }
	function consume( $q, $end, $w ) {
		return 2; }
	function enqueue( $r, $lc, $w, $path ) {
		switch( $w->nodeName ) { 
		case 'record':
			$this->clips[$w->getAttribute('id')] = $w;
			$this->clip_path[$w->getAttribute('id')] = $this->dnpath;
			return 0;
		case 'title':
			$this->title = $w;
			return 0;
		case 'feature':
			$this->skin = $w;
			return 0;
		case 'pith':
			$this->pith = $w;
			return 0;
		case 'date':
			$this->date = $w;
			return 0;
		}
		return 0;
	}
};

class array_cycler {
	public $a, $n;
	function __construct( $a ) {
		$this->a = $a;
		$this->n = 0;
	}
	function read() {
		$m = null;
		if( $this->n == count($this->a) ) {
			return false; }
		$m = $this->a[$this->n];
		$this->n += 1;
		return $m;
	}
};


class tgc_test_c__cell {
	public $tsq, $tsr, $tsw, $tsidents;
	public $dn;

	public $path, $env;
	public $config;
	public $n, $a, $sanity;
	function __construct($path,$env,$config) {
		$s = $n = $m = null;
		$this->path = $path;
		$this->env = $env;
		$this->config = $config;
//		$this->n = opendir( $this->config->path );

//		$s = [ ];
//		while(1) {
//			$m = readdir($n);
//			if( $m === false ) break;
//			$s[] = $m;
//		}
		//array_sort( $s );
//		$this->n = new array_cycler($s); //opendir( $this->config->path );
		//$_SERVER['DOCUMENT_ROOT'] );
		$this->sanity = 0;
		$this->onion = new onion_egg;
	}
	function start() {
		$this->n = new array_cycler( $this->config->hrefs ); //[ $con'/title' ] );
	}

	function read() {
		while(1) {
			$this->a = $this->n->read(); //readdir($this->n);
//			$this->a = readdir($this->n);
			if( $this->a === false ) {
				return false; }
			if( $this->a[0] != '.' ) break;
			//if( strtoupper( substr($this->a,strlen($this->a)-strlen($this->env->file_ext)) ) == strtoupper( $this->env->file_ext ) ) {
			//	break; }
		}
		return true;
	}

	function tap( $env, $tsr ) {
		$d = null;
		if( $tsr ) {
			if( ! $this->read() ) { $this->sanity = 1; return; }
			if( $this->b == $this->patients ) return;
			$this->b += 1;
		} else {
			$this->b = 1;
		}
		$env->enqueue_surreal( $this, [ 'red-atom:cache_control_value', 'red-atom:page_name', 'red-atom:dictionary', 'red-atom:title', 'red-atom:feature', 'red-atom:date', 'red-atom:a.onion', 'red-atom:play' ]  );
		sane_enqueue_subtree( $env, $this->dn, $this->path );
		$this->onion->dn = load_eggsgml_file( $this->config->path . '/' . $this->a . '.xgml' );
		$this->onion->dnpath = $this->path;
		$this->onion->title = $this->onion->feature = $this->onion->pith = $this->onion->date = null;
		$this->onion->clips = [ ];
		$env->enqueue_surreal( $this->onion, [ ] );
	}
	function enqueue( $r, $env, $w, $path ) {
		$d = null;
		switch($w->nodeName) {
		case 'play':
			if( array_key_exists( $w->getAttribute('id'), $this->onion->clips ) ) {
				$r->egg = new subtag_egg( $this->onion->clips[$w->getAttribute('id')], $this->onion->clip_path[$w->getAttribute('id')] );
				return 2; }
			return 3;
		case 'dictionary':
			$r->egg = new tgctestc_dictionary;
			return 2;
		case 'cache_control_value':
			$r->egg = new tgctestc_cachecontrol($this->a);
			return 2;
		case 'page_name':
			$r->egg = new tgctestc_pagename($this->a);
			return 2;
		case 'a.onion':
			$r->egg = new href_egg( $w, $path, $this->a );
			return 1;
		case 'title':
			if( ! $this->onion->title ) return 3;
			$r->egg = new subtag_egg( $this->onion->title, $this->path );
			return 2;
		case 'feature':
			if( ! $this->onion->feature ) return 3;
			$r->egg = new subtag_egg( $this->onion->feature, $this->path );
			return 2;
		case 'date':
			if( ! $this->onion->date ) return 3;
			$r->egg = new subtag_egg( $this->onion->date, $this->path );
			return 2;
		}
		return 0;
	}
};

class tgctestc_ward {
	public $tsq, $tsr, $tsw;
	public $dn;
	public $m;
	function __construct($path,$env,$config) {
		$this->m = new tgc_test_c__cell($path,$env,$config);
	}
	function tap( $env, $tsr ) {
		if( $tsr ) {
			if( $this->m->sanity ) return;
		}
		$env->enqueue_surreal( $this, [ 'red-atom:cell' ] );
		sane_enqueue_subtree( $env, $this->dn, $this->m->path );
	}
	function enqueue( $r, $env, $w, $path ) {
		switch( $w->nodeName ) {
		case 'cell':
			$this->m->dn = $w;
			$this->m->patients = $w->getAttribute('patients');
			$r->egg = $this->m;
			return 1; //3;
		}
		return 0;
	}
};

class tgctestc__log {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;

	public $dn;

	public $path, $env;
	public $config;
	function __construct($path,$env,$config) {
		$this->path = $path;
		$this->env = $env;
		$this->config = $config;
	}
	function open() {
		return $this->m->read();
	}
	function tap( $env, $tsr ) {
		if( $tsr ) {
			return;
		}
		$env->enqueue_surreal( $this, [ 'red-atom:a' ] );
		sane_enqueue_subtree( $env, $this->dn, $this->path );
	}
	function enqueue( $r, $env, $w, $path ) {
		switch( $w->nodeName ) {
		case 'a':
			$this->config->hrefs[] = $w->getAttribute('href');
			return 3;
		}
		return 0;
	}
};

class tgctestc__asylum {
	public $tsq, $tsr, $tsw;

	public $dn;

	public $path, $env;
	public $m;
	function __construct($path,$env,$config) {
		$this->path = $path;
		$this->env = $env;
		$this->m = new tgctestc_ward($this->path,$this->env,$config);
	}
	function open() {
		return $this->m->read();
	}
	function tap( $env, $tsr ) {
		if( $tsr ) return;
		$this->m->m->start();
		if( ! $this->m->m->read() ) {
			//$this->NF = newframe( new tgc_test_c__empty, $q, $w );
			return;
		}
		$env->enqueue_surreal( $this, [ 'red-atom:ward', 'red-atom:empty' ] );
		sane_enqueue_subtree( $env, $this->dn, $this->path );
	}
	function enqueue( $r, $env, $w, $path ) {
		switch( $w->nodeName ) {
		case 'empty':
			return 0;
		case 'ward':
			$this->m->dn = $w;
			$r->egg = $this->m;
			return 1; //3;
		}
		return 0;
	}
};

class tgctestc {
	public $tsq, $tsr, $tsw;

	public $q;

	public $path, $config;
	function initialize($path,$w) {
		$this->path = $path;
		$this->dn = $w;
		$this->config = new asylum_config;
	}
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
		if( $tsr ) return;
		$env->enqueue_surreal( $this, [ 'red-atom:asylum', 'red-atom:log' ] );
		sane_enqueue_subtree( $env, $this->dn, $this->path );
	}
	function enqueue( $r, $env, $w, $path ) {
		$c = null;
		switch( $w->nodeName ) {
		case 'asylum':
			$c = new tgctestc__asylum($this->path,$env,$this->config);
			$c->dn = $w;
			$r->egg = $c;
			return 1;
		case 'log':
			$c = new tgctestc__log($this->path,$env,$this->config);
			$c->dn = $w;
			$r->egg = $c;
			return 1;
		}
		return 0;
	}
};

return new tgctestc;
?>
