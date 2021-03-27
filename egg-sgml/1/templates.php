<?php
#    templates.php - handler for Egg-SGML documents
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

//function load_file($m) {
//	$d = new DomDocument();
//	$d->load("asdf.html");
//	return $d;
//}

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
	function consume( $q, $end, $w ) {
		return 0; }
}

class echo_out {
	function write( $d ) {
		echo $d;
	}
}

function test($F) {
	$F->q->write("<!doctype html>");
	$x = $F->T->firstChild;
	if ( $x == null ) 
		return;
	while (1) {
		$h = 1;
		while ($h) {
//++
			if( $x->nodeType == 8 ) {
				break; }
//+++
			if ( $x->nodeType == 3 ) {
				$F->q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x->data ) ) );
				break; }
//			if x.nodeType == 7:
//				F_ = F
//				while 1:
//					if x.target in F_.V:
//						F.q.write( F_.V[x.target].replace('&','&amp;').replace('<','&lt;') )
//						break
//					F_ = F_.P
//					if F_ == None:
//						F.q.write( '&' + x.target + ';')
//						break
//				break
			$_F = $F;
			while (1) {
				$a = $_F->c->consume( $F->q, 0, $x );
				if( $a == 1 ) {
					$h = 0;
					break; }
				if( $a == 2 ) {
					$b = $x->firstChild;
					if( $b != null ) {
						$x = $b;
					} else {
						$h = 0; }
					break; }
				if( $a == 3 ) {
					$NF = $_F->c->NF;
					$_F->c->NF = null;
					$b = $NF->T->firstChild;
					if( $b == null ) {
						$h = 0;
						break; }
					$F->x = $x;
					$NF->P = $F;
					$F = $NF;
					$x = $b;
					$F->c->start( $F->q );
					break; }
				$_F = $_F->P;
//++
				if( $_F == null ) {
					break; }
//+++
			}
		}
		while (1) {
			if( $x->nodeType == 1 ) {
				$_F = $F;
				while (1) {
					if( $_F->c->consume( $F->q, 1, $x ) ) {
						break; }
					$_F = $_F->P; }
			}
			$b = $x->nextSibling;
			if( $b != null ) {
				$x = $b;
				break; }
			$b = $x->parentNode;
			if( $b === $F->T ) {
				if( $F->c->repeat($F->q) ) {
					$x = $F->T->firstChild;
					break; }
				$F = $F->P;
				if( $F == null ) {
					return; }
				$x = $F->x;
			} else {
				$x = $b;
			}
		}
	}
}

class tgc_sgml_source {
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume( $q, $end, $w ) {
		if( $end ) {
			if( $w->firstChild != null ) {
				$q->write('<b>&lt;/<u>' . $w->nodeName . '</u>></b>'); }
			return 1; }
		$q->write('<b>&lt;<u>' . $w->nodeName . '</u>');
		for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
		if( $w->firstChild == null ) {
			$q->write('/'); }
		$q->write('></b>');
		return 2; }
}

function strmerge( $a, $b, $c ) {
	if( $a != '' ) {
		if( $b != '' ) {
			return $a . $c . $b;
		} return $a;
	} return $b;
}

class tgc_test {
	public $path, $self_href;
	function __construct($path,$self_href) {
		$this->path = $path;
		$this->self_href = $self_href;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume( $q, $end, $w ) {
		# local $m, $f
		if( $w->nodeName == 'tag' ) {
			return 2; }
		if( $w->nodeName == 'a.site' ) {
			if( $end ) {
				if( $w->firstChild != null ) {
					$q->write('</a>'); }
				return 1; }
			$q->write('<a');
			if( $w->getAttribute('href') == $this->self_href ) {
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
			if( $w->firstChild == null ) {
				$q->write('/'); }
			$q->write('>');
			return 2; }
		if( $w->nodeName == 'include' ) {
			if ($end) return 1;
			$m = new DomDocument;
			$m->load( $this->path . "/" . $w->getAttribute('path') );
			$this->NF = newframe(new tgc,$q,$m);
			return 3; }
		if( $w->nodeName == 'showsource' ) {
			if ($end) return 1;
			$m = new DomDocument;
			$m->load( $this->path . "/" . $w->getAttribute('path') );
			$this->NF = newframe(new tgc_sgml_source,$q,$m);
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
		if( $w->nodeName == 'script' ) {
			if ($end) return 1;
			$q->write('<' . $w->nodeName);
			for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				$q->write(' ' . $w->attributes->item($m)->name);
				if( $w->attributes->item($m)->value != null ) {
					$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
			}
			$q->write('>');
			if( $w->firstChild != null ) {
				$q->write( $w->firstChild->value ); }
			$q->write('</script>');
			return 1; }
		if( $end ) {
			if( $w->firstChild != null ) {
				$q->write('</' . $w->nodeName . '>'); }
			return 1; }
		$q->write('<' . $w->nodeName);
		if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
		if( $w->firstChild == null ) {
			if( $w->nodeName == 'div' || $w->nodeName == 'i' ) {
				$q->write('></' . $w->nodeName);
			} else { $q->write('/'); }
		}
		$q->write('>');
		return 2; }
}

function main($doc,$self_href) {
	$d = new DomDocument();
	$d->strictErrorChecking = false;
	$d->load($doc);
	$k = newframe(new tgc_test(dirname(__FILE__,2),$self_href), new echo_out, $d);
	$k->P = null;
	test($k);
}

function check_shipyard_auth() {
	if( ! file_exists("../shipyard.txt") ) return true;
	$m = file_get_contents('../shipyard.txt');
	if( $m === false ) return true;
	if( array_key_exists( 'shipyard', $_COOKIE ) ) {
		if( $_COOKIE['shipyard'] === $m ) {
			return true; } }
	if( array_key_exists( 'shipyard', $_GET ) ) {
		if( $_GET['shipyard'] === $m ) {
			setcookie('shipyard',$m);
			return true; } }
}

function metamain() {
	if( $_SERVER['HTTPS'] ) {
		header( 'Strict-Transport-Security: max-age=333300; includeSubDomains; preload' );
		if( ! check_shipyard_auth() ) {
			main('../shipyard', '/shipyard');
			return; }
		if( $_GET['t'] == '/' ) {
			main('../index', $_GET['t']);
		} else if( $_GET['t'] == '/index' ) {
			header('Location:https://' . $_SERVER['HTTP_HOST'] . '/' );
		} else {
			main('..' . $_GET['t'], $_GET['t'] );
		}
	} else {
		header('Location:https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}
}

metamain();

?>