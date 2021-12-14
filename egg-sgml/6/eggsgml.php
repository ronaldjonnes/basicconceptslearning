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
	public $stack, $repeat;

	function enqueue( $egg, $repeat ) {
		$egg->tsq = $this->stack;
		$egg->tsr = $this->repeat;
		$this->stack = $egg;
		$this->repeat = $repeat;
	}

	function write( $a ) {
		$this->stack->write($a);
	}
	function unhandled_tag( $m ) {
		echo( "unhandled tag". $m->nodeType. $m->nodeName );
	}
};

class egg {
	public $tsq, $tsr, $tsw;
	public $writernode;
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a); 
	}
	function tap( $env, $tsr ) {
	}
};

class subrootdomegg {
	public $tsq, $tsr;
	public $writernode;
	public $tgcnode, $dn;
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a);
	}
	function tap( $env, $str ) {
		if( $str ) {
			if( $this->tgcnode->tgc->repeat( $env ) ) {
			} else return;
		} else {
			$this->tgcnode->tgc->start($env);
		}
		$env->enqueue( $this, 1 );
		for( $w = $this->dn->childNodes->length ; $w > 0 ; $w -= 1 ) {
			$c = $this->dn->childNodes->item($w-1);
			$a = new domegg;
			$a->tgcnode = $this->tgcnode;
			$a->dn = $c;
			$a->writernode = $this->writernode;
			$env->enqueue( $a, 0, 0 );
		}
		return;
	}
};

class module1egg {
	public $tsq, $tsr;
	public $writernode, $tgcnode;
	public $tgc;
	public $q;
	function write($a) {
		if( $this->writernode ) $this->writernode->q->write($a);
	}
	function tap( $env, $str ) {
	}
};

class domegg {
	public $tsq, $tsr;
	public $writernode;

	public $tgcnode;
	public $tgc, $dn;

	public $q;
	function write($r) { 
		if( $this->writernode ) $this->writernode->q->write($r); 
	}
	function tap( $env, $str ) {
		$w = 0; $b = $f = $a = $c = null;
		switch( $this->dn->nodeType ) {
		case 3:
			$this->tgcnode->tgc->consume_text( $env, $this->dn->data );
			break;
		case 9: 
			if( $str ) {
				return; }
			goto _2b;
		case 8:
			break;
		default:
			while( $str ) {
				$a = $this->tgcnode;
				do switch( $a->tgc->consume( $env, true, $this->dn ) ) {
				case 0: 
					$a = $a->tgcnode;
					if( $a == null ) {
						$env->unhandled_tag( $this->dn );
						return;
					}
					break;
				case 1: default:
					//if( $this->tgcnode == $env->stack ) {
					//	if( $this->tgcnode->tgc->repeat($env) ) {
					//		$env->enqueue( $this, 1, $this->q != null );
					//	}
					//}
					return;
				} while(1);
			}
			$a = $this->tgcnode;
			while(1) switch( $a->tgc->consume( $env, false, $this->dn ) ) {
			case 0:
				$a = $a->tgcnode;
				if( $a == null ) {
					$env->unhandled_tag( $this->dn );
					return;
				}
				break;
			case 1: goto _1;
			case 2: goto _2;
			case 3: default: 
				$env->enqueue( $this, 1 );
				$d = new subrootdomegg;
				$this->tgc = $a->tgc->NF->c;
				$d->tgcnode = $this;
				$d->dn = $a->tgc->NF->T;
				if( $a->tgc->NF->q == $env ) {
					$d->writernode = $this->writernode;
				} else {
					$this->q = $a->tgc->NF->q;
					$d->writernode = $this;
				}

				$env->enqueue( $d, 0 );
				return;
			case 4:
				$env->enqueue( $a->tgc->NF, 0 );
				return;
			}
		}
		return;
	_1:
		$env->enqueue( $this, 1, 0 );
		return;
	_2:
		$env->enqueue( $this, 1 );
	_2b:
		for( $w = $this->dn->childNodes->length ; $w > 0 ; $w -= 1 ) {
			$c = $this->dn->childNodes->item($w-1);
			$a = new domegg;
			$a->tgcnode = $this->tgcnode;
			$a->dn = $c;
			$a->writernode = $this->writernode;
			$env->enqueue( $a, 0 );
		}
		return;
	_3:
		$d = new domegg;
		$d->tgcnode = $d;
		$d->parent_tgcnode = $this->tgcnode;
		$d->tgc = $f; 
		$d->dn = $b;
		$env->enqueue( $d, 0, $d->q != null );
		for( $w = $b->childNodes->length ; $w > 0 ; $w -= 1 ) {
			$c = $b->childNodes->item($w-1);
			$a = new domegg;
			$a->tgcnode = $d;
			$a->dn = $c;
			$env->enqueue( $a, 0 );
		}
		return;
	_4:
		$b = $this->NF->T;
		for( $w = $b->childNodes->length ; $w > 0 ; $w -= 1 ) {
			$c = $b->childNodes->item($w - 1);
			$a = new domegg;
			$a->tgcnode = $a;
			$a->parent_tgcnode = $this->tgcnode;
			$a->tgc = $this->NF->c;
			$a->dn = $c;
			$env->enqueue( $a, 0, 0 );
		}
		return;
	}
}

function eggsgml_2( $env ) {
	while( $env->stack ) {
		$c = $env->stack;
		$d = $env->repeat;
		$env->repeat = $c->tsr;
		$env->stack = $c->tsq;

		$c->tap( $env, $d );
	}
}

function eggsgml( $F ) {
	$c = null;
	$env = new chicken();
	$c = new module1egg;
	$c->tgc = $F->c;
	$c->q = $F->q;
	$env->enqueue( $c, 0 );
	$c = new domegg;
	$c->tgcnode = $env->stack;
	$c->dn = $F->T;
	$c->writernode = $env->stack;
	$env->enqueue( $c, 0 );
	eggsgml_2( $env );
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
