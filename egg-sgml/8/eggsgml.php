<?php

#    eggsgml.php - Egg SGML nuts & bolts
#    Copyright 2020, 2021 Brian Jonnes
#
#    Egg-SGML is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, version 3 of the License.
#
#    Egg-SGML is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with Egg-SGML.  If not, see <https://www.gnu.org/licenses/>.


class chicken {
	public $libconfig;
	public $stack, $repeat, $idents, $perfectbliss;
	public $diplomats;

	public $c;

	function __construct( $lc ) {
		$this->libconfig = $lc;
		$this->diplomats = [ [ ] ];
		$this->stack = null;
	}

	function talk_yourself_to_sleep() {
		$h = null; $d = $y = 0;
		while( $this->stack ) {
			$this->c = $this->stack;
			$d = $this->repeat;
			$h = $this->idents;
			$y = $this->perfectbliss;
			$this->repeat = $this->c->tsr;
			$this->stack = $this->c->tsq;
			$this->idents = $this->c->tsidents;
			$this->perfectbliss = $this->c->tsperfectbliss;
			if( $y ) array_shift( $this->diplomats );
			else if( $h ) $this->sane_unregister( $h );

			$this->c->tap( $this, $d );
		}
	}

	function diplomat( $m ) {
		if( ! array_key_exists( $m, $this->diplomats[0] ) ) return null;
		return $this->diplomats[0][$m][count($this->diplomats[0][$m])-1];
	}
	function sane_enqueue( $egg, $perfectbliss, $repeat, $idents ) {
		$egg->tsq = $this->stack;
		$egg->tsr = $this->repeat;
		$egg->tsidents = $this->idents;
		$egg->tsperfectbliss = $this->perfectbliss;
		$this->stack = $egg;
		$this->repeat = $repeat;
		$this->idents = $idents;
		$this->perfectbliss = $perfectbliss;
		if( $perfectbliss ) { array_unshift( $this->diplomats, [ ] ); }
		if( $idents ) $this->sane_register( $egg, $idents );
	}
	function enqueue_surreal( $egg, $idents ) {
		$this->sane_enqueue( $egg, 0, $this->c == $egg, $idents );
	}
	function enqueue_perfectbliss( $egg, $idents ) {
		$this->sane_enqueue( $egg, 1, 1, $idents );
	}

	function write( $a ) {
		$this->diplomat('earring')->write($a);
		//$this->stack->write($a);
	}
	function unhandled_tag( $m ) {
		echo( "unhandled tag". $m->nodeType. $m->nodeName );
	}

	function sane_register( $egg, $idents ) {
		$j = null;
		foreach( $idents as $j ) {
			$this->diplomats[0][$j][] = $egg; }
	}
	function sane_unregister( $idents ) {
		$j = null;
		foreach( $idents as $j ) {
			array_pop( $this->diplomats[0][$j] );
			if( count( $this->diplomats[0][$j] ) == 0 ) {
				unset( $this->diplomats[0][$j] ); }
		}
	}
};

class sane_enqueue_result {
	public $egg;
};

function sane_enqueue_subtree($env,$T,$path) {
	$b = null; $h = null; $r = new sane_enqueue_result;
	$c = $T->lastChild;
	if ( $c == null ) 
		return;
	while (1) {
		while (1) {
			if( $c->nodeType == 1 ) if( array_key_exists( 'red-atom:' . $c->nodeName, $env->diplomats[0] ) ) {
				$h = $env->diplomats[0]['red-atom:' . $c->nodeName];
				switch( $h[count($h)-1]->enqueue( $r, $env->libconfig, $c, $path ) ) {
				case 0: default: break;
				case 1:
					$env->enqueue_surreal( $r->egg, [ ] );
					break;
				case 2:
					$env->enqueue_surreal( $r->egg, [ ] );
					goto a;
				case 3:
					goto a;
					//enqueue_subtree( $env, $c );
				}
				break;
			}
			$h = new tag_egg($c,$path);
			//domegg;
			//$h->dn = $c;
			$env->enqueue_surreal( $h, [ ] );
			break;
		a:	$b = $c->lastChild;
			if( $b != null ) {
				$c = $b;
			} else {
				break; }
		}
		while (1) {
			$b = $c->previousSibling;
			if( $b != null ) {
				$c = $b;
				break; }
			$b = $c->parentNode;
			if( $b === $T ) {
				return;
			} else {
				$c = $b;
			}
		}
	}
}

class egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	function tap( $env, $tsr ) {
	}
};

class tgc_tag_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	public $dn, $path;
	function tap( $env, $str ) {
		if( $str ) {
			if( $env->diplomat('tgc')->tgc->repeat( $env ) ) {
			} else return;
		} else {
			$env->diplomat('tgc')->tgc->start($env);
		}
		$env->enqueue_surreal( $this, [ ] );
		sane_enqueue_subtree( $env, $this->dn, $this->path );
		return;
		for( $w = $this->dn->childNodes->length ; $w > 0 ; $w -= 1 ) {
			$c = $this->dn->childNodes->item($w-1);
			$a = new tag_egg($c,$this->path);
			//$a->tgcnode = $this->tgcnode;
			//$a->dn = $c;
			//$a->writernode = $this->writernode;
			$env->enqueue_surreal( $a, [ ] );
		}
		return;
	}
};

class tgc_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;
	public $tgc;
	public $q;
	function __construct($tgc) {
		$this->tgc = $tgc; }
	function write($a) {
		$this->q->write($a); }
	function tap( $env, $str ) {
	}
};

class tag_egg {
	public $tsq, $tsr, $tsw, $tsidents, $tsperfectbliss;

	public $tgc, $dn, $path;

	public $q;
	function __construct( $dn, $path ) {
		$this->dn = $dn;
		$this->path = $path;
	}
	function write($r) { 
		$this->q->write($r); 
	}
	function tap( $env, $str ) {
		$w = $h = 0; $b = $f = $a = $c = null;
		switch( $this->dn->nodeType ) {
		case 3:
			$env->diplomat('tgc')->tgc->consume_text( $env, $this->dn->data );
			//$this->tgcnode->tgc->consume_text( $env, $this->dn->data );
			break;
		case 9: 
			if( $str ) {
				return; }
			goto _2b;
		case 8:
			break;
		default:
			while( $str ) {
				$h = count( $env->diplomats[0]['tgc'] );
				do {
					$a = $env->diplomats[0]['tgc'][$h-1]; 
					switch( $a->tgc->consume( $env, true, $this->dn ) ) {
					case 0: 
						$h -= 1;
						if( $h == 0 ) {
						//$a = $a->tgcnode;
						//if( $a == null ) {
							$env->unhandled_tag( $this->dn );
							return;
						}
						break;
					case 1: default:
						return;
					}
				} while(1);
			}
			$h = count( $env->diplomats[0]['tgc'] );
			// $this->tgcnode;
			while(1) {
				$a = $env->diplomats[0]['tgc'][$h-1];
				switch( $a->tgc->consume( $env, false, $this->dn ) ) {
				case 0:
					$h = $h - 1;
					if( $h == 0 ) {
					//$a = $a->tgcnode;
					//if( $a == null ) {
						$env->unhandled_tag( $this->dn );
						return;
					}
					break;
				case 1: goto _1;
				case 2: goto _2;
				case 3: default:
					$this->tgc = $a->tgc->NF->c;
					if( $a->tgc->NF->q == $env ) {
						//$d->writernode = $this->writernode;
						$env->enqueue_surreal( $this, [ 'tgc' ] );
					} else {
						$this->q = $a->tgc->NF->q;
						$env->enqueue_surreal( $this, [ 'tgc', 'earring' ] );
						//$d->writernode = $this;
					}
					$d = new tgc_tag_egg;
					$d->dn = $a->tgc->NF->T;
					$d->path = $this->path;

					//$d->tgcnode = $this;
					$env->enqueue_surreal( $d, [ ] );
					return;
				case 4:
					$env->enqueue_surreal( $a->tgc->NF, [ ] );
					return;
				}
			}
		}
		return;
	_1:
		$env->enqueue_surreal( $this, [ ] );
		return;
	_2:
		$env->enqueue_surreal( $this, [ ] );
	_2b:
		sane_enqueue_subtree( $env, $this->dn, $this->path );
		return;
		for( $w = $this->dn->childNodes->length ; $w > 0 ; $w -= 1 ) {
			$c = $this->dn->childNodes->item($w-1);
			$a = new tag_egg( $c, $this->path ); 
			//domegg;
			$a->tgcnode = $this->tgcnode;
			//$a->dn = $c;
			$a->writernode = $this->writernode;
			$env->enqueue_surreal( $a, [ ] );
		}
		return;
	}
}

function eggsgml( $lc, $F, $path ) {
	$c = null;
	$env = new chicken($lc);
	$c = new tgc_egg($F->c);
	$c->q = $F->q;
	$env->enqueue_surreal( $c, [ 'tgc', 'earring' ] );
	$c = new tag_egg( $F->T, $path );
	//domegg;
	//$c->tgcnode = $env->stack;
	//$c->dn = $F->T;
	//$c->writernode = $env->stack;
	$env->enqueue_surreal( $c, [ ] );
	$env->talk_yourself_to_sleep();
}


class frame {
	public $T;
	public $c;
	public $q;
	public $V;
}

class dev_null {
	function write($m) {
	}
}

class buffer_out {
	public $a;
	function write($b) {
		$this->a .= $b;
	}
}

function newframe($c,$q,$T) {
	$NF = new frame();
	$NF->T = $T;
	$NF->c = $c;
	$NF->q = $q;
	$NF->V = array();
	return $NF;
}

class tgc {
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		return 0; }
}

class echo_out {
	function write( $d ) {
		echo $d;
	}
}

function sr_amp_lt( $x ) {
	return str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) );
}

function sr_amp_quot( $x ) {
	return str_replace('"','&quot;', str_replace( '&', '&amp;', $x ) );
}

function sr_25( $x, $k ) {
	$d = 00; $n = '';
	for( $d = 0; $d < strlen($x); $d += 1 ) {
		if( $x[$d] == '%' || strpos($k,$x[$d]) !== false ) {
			$n .= '%' . str_pad( dechex(ord($x[$d])), 2, '0', STR_PAD_LEFT );
		} else $n .= $x[$d];
	}
	return $n;
}

function write_attributes( $q, $w, $f ) {
	$m = 00;
	for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
		if( array_search( $w->attributes->item($m)->name, $f ) === false ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
	}
}
function attribute_with_inival( $w, $N, $initial ) {
		if( attribute_exists( $w, $N ) ) {
			return $w->getAttribute($N); }
// At this point, the coder thinks to themselves, is this the best way to proceed?
		return $initial;
// It'll do for now, I suppose.
}

//NOT in 70331
//FOUND in 70333
if(PHP_VERSION_ID < 70333 ) {
  function mb_chr($j) { /* Documentation indicates this should be in 7.2 */
	if($j==39) return '\'';
	return html_entity_decode('&#'.$j.';'); }
}

function eggsgml_descendent($T,$t) {
	$x = $T->firstChild;
	if ( $x == null ) 
		return;
	while (1) {
		while (1) {
			if( $x->nodeType == 8 ) {
				break; }
			if ( $x->nodeType == 3 ) {
				break; }
			if( $x->nodeName == $t ) {
				return $x; }
			$b = $x->firstChild;
			if( $b != null ) {
				$x = $b;
			} else {
				break; }
		}
		while (1) {
			$b = $x->nextSibling;
			if( $b != null ) {
				$x = $b;
				break; }
			$b = $x->parentNode;
			if( $b === $T ) {
				return null;
			} else {
				$x = $b;
			}
		}
	}
}
?>
