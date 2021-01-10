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
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&amp;lt;", str_replace( "&", "&amp;amp;", $x ) ) ); }
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

class tgc_generic {
	public $path, $self_href;
	function __construct($path,$self_href) {
		$this->path = $path;
		$this->self_href = $self_href;
		$this->clips = [ ];
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		# local $m, $f
		if( $w->nodeName == 'doctype' ) {
			if( $end ) return 1;
			$q->write('<!' . $w->getAttribute('raw') . '>');
			return 2; }
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
			$m = load_eggsgml_file( $this->path . "/" . $w->getAttribute('path') );
			$this->NF = newframe(new tgc,$q,$m);
			return 3; }
		if( $w->nodeName == 'showsource' ) {
			if ($end) return 1;
			$m = load_eggsgml_file( $this->path . "/" . $w->getAttribute('path') );
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
?>