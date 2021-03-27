<?php
#    parser.php - Egg SGML
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

class eggsgml_tag_receiver {
	function new_tag_formation( $j ) { }
	function canceltag() {	}
	function complete_tag( $self_closing ) {	}
	function text( $j ) { }
	function tagattr( $j, $d ) { }
	function closing_tag( $j ) { }
	function complete_dt( $dt, $comment ) { }
	function htmlent( $m ) { }
};

class diagnostic_tagreceiver {
	function __construct() {
		$this->tag = 00; }
	function new_tag_formation( $j ) {
		echo '&lt;new_tag name="' . $j . '"/>'; }
	function canceltag() {
		echo '&lt;cancel_tag/>'; }
	function complete_tag( $self_closing ) {
		echo '&lt;complete_tag'; if( $self_closing ) echo ' self_closing'; echo '/>'; }
	function text( $j ) {
		echo '&lt;text value="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $j ) ) ) . '"/>'; }
	function tagattr( $j, $d ) {
		echo '&lt;attribute name="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $j ) ) ) . '" value="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $d ) ) ) . '"/>'; }
	function closing_tag( $j ) {
		echo '&lt;closing_tag name="' . $j . '"/>'; }
	function complete_dt( $dt, $comment ) {
		echo '&lt;dt value="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $dt ) ) ) . '"/>';
		echo '&lt;comment value="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $comment ) ) ) . '"/>';
	}
	function htmlent( $m ) {
		echo '&lt;htmlent value="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $m ) ) ) . '"/>';
	}
};

class test_tagreceiver {
	function __construct() {
		$this->w = new DomDocument;
		$this->c = $this->w; //$this->w->documentElement;
		$this->attrs = 00; $this->tag = 00; }
	function new_tag_formation( $j ) {
		$this->tag = $j;
		$this->attrs = [ ]; }
	function canceltag() {	}
	function complete_tag( $self_closing ) {
		$x = $j = $d = 00;
		echo '&lt;' . $this->tag;
		foreach( $this->attrs as $j => $d ) {
			echo ' ' . $j . '="' . str_replace('<','&lt;',str_replace('"',"&amp;quot;", str_replace( "&", "&amp;amp;", $d ) ) ) . '"';
		}
		if( $self_closing ) echo '/';
		echo '>';
		
		$x = $this->w->createElement($this->tag);
		$this->c->appendChild( $x );

		foreach( $this->attrs as $j => $d ) {
			$x->setAttribute( $j, $d ); }
		if( ! $self_closing ) {
			$this->c = $x; }
	}
	function text( $j ) {
		$this->c->appendChild( $this->w->createTextNode( $j ) );
		echo str_replace('<','&amp;lt;',str_replace('&','&amp;amp;',$j));
	}
	function tagattr( $j, $d ) {
		$this->attrs[$j] = $d; }
	function closing_tag( $j ) {
		$x = $this->c;
		while(1) {
			if( ! $x ) {
				$this->c->appendChild( $this->w->createTextNode( '</' . $j . '>' ) );
				break; }
			if( strtoupper( $x->nodeName ) == strtoupper( $j ) ) {
				$this->c = $x->parentNode;
				break; }
			$x = $x->parentNode; }

		echo '&lt;/' . $j . '>'; }
}	

class W3CDOM_tagreceiver {
	function __construct() {
		$this->w = new DomDocument;
		$this->c = $this->w; //$this->w->documentElement;
		$this->attrs = 00; $this->tag = 00; 
		$this->htmlent = [ 'quot' => 34, 'amp' => 38, 'lt' => 60, 'gt' => 62, 'nbsp' => 160, 'iexcl' => 161, 'cent' => 162, 'pound' => 163, 'curren' => 164, 'yen' => 165, 'brvbar' => 166, 'sect' => 167, 'uml' => 168, 'copy' => 169, 'ordf' => 170, 'laquo' => 171, 'not' => 172, 'shy' => 173, 'reg' => 174, 'macr' => 175, 'deg' => 176, 'plusmn' => 177, 'sup2' => 178, 'sup3' => 179, 'acute' => 180, 'micro' => 181, 'para' => 182, 'middot' => 183, 'cedil' => 184, 'sup1' => 185, 'ordm' => 186, 'raquo' => 187, 'frac14' => 188, 'frac12' => 189, 'frac34' => 190, 'iquest' => 191, 'Agrave' => 192, 'Aacute' => 193, 'Acirc' => 194, 'Atilde' => 195, 'Auml' => 196, 'Aring' => 197, 'AElig' => 198, 'Ccedil' => 199, 'Egrave' => 200, 'Eacute' => 201, 'Ecirc' => 202, 'Euml' => 203, 'Igrave' => 204, 'Iacute' => 205, 'Icirc' => 206, 'Iuml' => 207, 'ETH' => 208, 'Ntilde' => 209, 'Ograve' => 210, 'Oacute' => 211, 'Ocirc' => 212, 'Otilde' => 213, 'Ouml' => 214, 'times' => 215, 'Oslash' => 216, 'Ugrave' => 217, 'Uacute' => 218, 'Ucirc' => 219, 'Uuml' => 220, 'Yacute' => 221, 'THORN' => 222, 'szlig' => 223, 'agrave' => 224, 'aacute' => 225, 'acirc' => 226, 'atilde' => 227, 'auml' => 228, 'aring' => 229, 'aelig' => 230, 'ccedil' => 231, 'egrave' => 232, 'eacute' => 233, 'ecirc' => 234, 'euml' => 235, 'igrave' => 236, 'iacute' => 237, 'icirc' => 238, 'iuml' => 239, 'eth' => 240, 'ntilde' => 241, 'ograve' => 242, 'oacute' => 243, 'ocirc' => 244, 'otilde' => 245, 'ouml' => 246, 'divide' => 247, 'oslash' => 248, 'ugrave' => 249, 'uacute' => 250, 'ucirc' => 251, 'uuml' => 252, 'yacute' => 253, 'thorn' => 254, 'yuml' => 255, 'OElig' => 338, 'oelig' => 339, 'Scaron' => 352, 'scaron' => 353, 'Yuml' => 376, 'fnof' => 402, 'circ' => 710, 'tilde' => 732, 'Alpha' => 913, 'Beta' => 914, 'Gamma' => 915, 'Delta' => 916, 'Epsilon' => 917, 'Zeta' => 918, 'Eta' => 919, 'Theta' => 920, 'Iota' => 921, 'Kappa' => 922, 'Lambda' => 923, 'Mu' => 924, 'Nu' => 925, 'Xi' => 926, 'Omicron' => 927, 'Pi' => 928, 'Rho' => 929, 'Sigma' => 931, 'Tau' => 932, 'Upsilon' => 933, 'Phi' => 934, 'Chi' => 935, 'Psi' => 936, 'Omega' => 937, 'alpha' => 945, 'beta' => 946, 'gamma' => 947, 'delta' => 948, 'epsilon' => 949, 'zeta' => 950, 'eta' => 951, 'theta' => 952, 'iota' => 953, 'kappa' => 954, 'lambda' => 955, 'mu' => 956, 'nu' => 957, 'xi' => 958, 'omicron' => 959, 'pi' => 960, 'rho' => 961, 'sigmaf' => 962, 'sigma' => 963, 'tau' => 964, 'upsilon' => 965, 'phi' => 966, 'chi' => 967, 'psi' => 968, 'omega' => 969, 'thetasym' => 977, 'upsih' => 978, 'piv' => 982, 'ensp' => 8194, 'emsp' => 8195, 'thinsp' => 8201, 'zwnj' => 8204, 'zwj' => 8205, 'lrm' => 8206, 'rlm' => 8207, 'ndash' => 8211, 'mdash' => 8212, 'lsquo' => 8216, 'rsquo' => 8217, 'sbquo' => 8218, 'ldquo' => 8220, 'rdquo' => 8221, 'bdquo' => 8222, 'dagger' => 8224, 'Dagger' => 8225, 'bull' => 8226, 'hellip' => 8230, 'permil' => 8240, 'prime' => 8242, 'Prime' => 8243, 'lsaquo' => 8249, 'rsaquo' => 8250, 'oline' => 8254, 'frasl' => 8260, 'euro' => 8364, 'image' => 8465, 'weierp' => 8472, 'real' => 8476, 'trade' => 8482, 'alefsym' => 8501, 'larr' => 8592, 'uarr' => 8593, 'rarr' => 8594, 'darr' => 8595, 'harr' => 8596, 'crarr' => 8629, 'lArr' => 8656, 'uArr' => 8657, 'rArr' => 8658, 'dArr' => 8659, 'hArr' => 8660, 'forall' => 8704, 'part' => 8706, 'exist' => 8707, 'empty' => 8709, 'nabla' => 8711, 'isin' => 8712, 'notin' => 8713, 'ni' => 8715, 'prod' => 8719, 'sum' => 8721, 'minus' => 8722, 'lowast' => 8727, 'radic' => 8730, 'prop' => 8733, 'infin' => 8734, 'ang' => 8736, 'and' => 8743, 'or' => 8744, 'cap' => 8745, 'cup' => 8746, 'int' => 8747, 'there4' => 8756, 'sim' => 8764, 'cong' => 8773, 'asymp' => 8776, 'ne' => 8800, 'equiv' => 8801, 'le' => 8804, 'ge' => 8805, 'sub' => 8834, 'sup' => 8835, 'nsub' => 8836, 'sube' => 8838, 'supe' => 8839, 'oplus' => 8853, 'otimes' => 8855, 'perp' => 8869, 'sdot' => 8901, 'lceil' => 8968, 'rceil' => 8969, 'lfloor' => 8970, 'rfloor' => 8971, 'lang' => 9001, 'rang' => 9002, 'loz' => 9674, 'spades' => 9824, 'clubs' => 9827, 'hearts' => 9829, 'diams' => 9830 ]; }
	function new_tag_formation( $j ) {
		$this->tag = $j;
		$this->attrs = [ ]; }
	function canceltag() {	}
	function complete_tag( $self_closing ) {
		$x = $j = $d = 00;
		
		$x = $this->w->createElement($this->tag);
		$this->c->appendChild( $x );

		foreach( $this->attrs as $j => $d ) {
			$x->setAttribute( $j, $d ); }
		if( ! $self_closing ) {
			$this->c = $x; }
	}
	function text( $j ) {
		$this->c->appendChild( $this->w->createTextNode( $j ) );
	}
	function tagattr( $j, $d ) {
		$this->attrs[$j] = $d; }
	function closing_tag( $j ) {
		$x = $this->c;
		while(1) {
			if( ! $x ) {
				$this->c->appendChild( $this->w->createTextNode( '</' . $j . '>' ) );
				break; }
			if( strtoupper( $x->nodeName ) == strtoupper( $j ) ) {
				$this->c = $x->parentNode;
				break; }
			$x = $x->parentNode; }
		}
	function complete_dt( $dt, $comment ) {
		if( $dt != '' ) {
			$x = $this->w->createElement('doctype');
			$x->setAttribute( 'raw', $dt );
			$this->c->appendChild( $x );
		}
	}
	function attrent( $m ) {
		$u = 00;
		if( array_key_exists( $m, $this->htmlent ) ) {
			$u = mb_chr( $this->htmlent[$m] );
		} else {
			$u = '&' . $m . ';'; }
		return $u;
	}
	function htmlent( $m ) {
		if( array_key_exists( $m, $this->htmlent ) ) {
			$u = mb_chr( $this->htmlent[$m] );
		} else {
			$u = '&' . $m . ';'; }
		$this->text($u); }
}	

class eggsgml_parser {
	function __construct($T) {
		$this->sR = ''; $this->s1 = $this->s2 = $this->s3 = $this->s4 = '';
		$this->w = 1000;
		$this->T = $T;
	}
	function sp_tab_nl( $c ) {
		return $c == ' ' || $c == "\t" || $c == "\n" || $c == "\r";
	}
	function priv_process_chunk_sub( $m ) {
		$c = 0; $a = $b = 00;
		while( $c < strlen($m) ) {
			switch( $this->w ) {
			case 1000:
				if( ord( $m[$c] ) == 239 ) {
					$c += 1;
					$this->w = 1001;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->w = 1;
				} else {
					$this->T->text( $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 1001:
				if( ord( $m[$c] ) == 187 ) {
					$c += 1;
					$this->w = 1002;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->T->text( chr(239) );
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->T->text( chr(239) );
					$this->w = 1;
				} else {
					$this->T->text( chr(239) . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 1002:
				if( ord( $m[$c] ) == 191 ) {
					$c += 1;
					$this->w = 0;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->T->text( chr(239) . chr(187) );
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->T->text( chr(239) . chr(187) );
					$this->w = 1;
				} else {
					$this->T->text( chr(239) . chr(187) . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 0:
				$a = strpos( $m, '<', $c );
				if( ! ( $a === false ) ) {
					$b = strpos( $m, '&', $c );
					if( ! ( $b === false ) && $b < $a ) {
						if( $b > $c ) $this->T->text( substr( $m, $c, $b - $c ) );
						$c = $b + 1;
						$this->w = 1;
						break; }
					if( $a > $c ) $this->T->text( substr( $m, $c, $a - $c ) );
					$c = $a + 1;
					$this->w = 10;
					break; }
				$a = strpos( $m, '&', $c );
				if( ! ( $a === false ) ) {
					if( $a > $c ) $this->T->text( substr( $m, $c, $a - $c ) );
					$c = $a + 1;
					$this->w = 1;
					break; }
				$this->T->text( substr( $m, $c ) );
				return 1;
			case 1:
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->s1 = $m[$c];
					$c += 1;
					$this->w = 2;
				} else if( $m[$c] == '#' ) {
					$c += 1;
					$this->w = 3;
				} else if( $m[$c] == '<' ) {
					$this->T->text( '&' );
					$c += 1;
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$this->T->text( '&' );
					$c += 1;
				} else {
					$this->T->text( '&' . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 2:
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->s1 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->T->htmlent( $this->s1 );
					$c += 1;
					$this->w = 0;
				} else if( $m[$c] == '<' ) {
					$this->T->text( '&' . $this->s1 );
					$c += 1;
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$this->T->text( '&' . $this->s1 );
					$c += 1;
					$this->w = 1;
				} else {
					$this->T->text( '&' . $this->s1 . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 3:
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->s1 = $m[$c];
					$c += 1;
					$this->w = 4;
				} else if( $m[$c] == '<' ) {
					$this->T->text( '&#' );
					$c += 1;
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$this->T->text( '&#' );
					$c += 1;
					$this->w = 1;
				} else {
					$this->T->text( '&#' . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 4: // &#0
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->s1 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->T->text( mb_chr( $this->s1 ) );
					$c += 1;
					$this->w = 0;
				} else if( $m[$c] == '<' ) {
					$this->T->text( '&#' . $this->s1 );
					$c += 1;
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$this->T->text( '&#' . $this->s1 );
					$c += 1;
					$this->w = 1;
				} else {
					$this->T->text( '&#' . $this->s1 . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 10: // <
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->s1 = $m[$c];
					$c += 1;
					$this->w = 11;
				} else if( $m[$c] == '/' ) {
					$c += 1;
					$this->w = 101;
				} else if( $m[$c] == '!' ) {
					$c += 1;
					$this->s1 = $this->s2 = '';
					$this->w = 40;
				} else if( $m[$c] == '<' ) {
					$this->T->text( '<' );
					$c += 1;
				} else if( $m[$c] == '&' ) {
					$this->T->text( '&' );
					$c += 1;
					$this->w = 1;
				} else {
					$this->T->text( '<' . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 101: // </
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->s1 = $m[$c];
					$c += 1;
					$this->w = 102;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->T->text( '</' );
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->T->text( '</' );
					$this->w = 1;
				} else {
					$this->T->text( '</' . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 102: // </a
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->s1 .= $m[$c];
					$c += 1;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR = $m[$c];
					$c += 1;
					$this->w = 103;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->closing_tag( $this->s1 );
					$this->w = 0;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->T->text( '</' . $this->s1 );
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->T->text( '</' . $this->s1 );
					$this->w = 1;
				} else {
					$this->T->text( '</' . $this->s1 . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 103: // </a.
				if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->closing_tag( $this->s1 );
					$this->w = 0;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->T->text( '</' . $this->s1 . $this->sR );
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->T->text( '</' . $this->s1 . $this->sR );
					$this->w = 1;
				} else {
					$this->T->text( '</' . $this->s1 . $this->sR . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 11: // <a
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->s1 .= $m[$c];
					$c += 1;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR = $m[$c];
					$c += 1;
					$this->T->new_tag_formation( $this->s1 );
					$this->w = 12;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->new_tag_formation( $this->s1 );
					$this->T->complete_tag(0);
					$this->w = 0;
				} else if( $m[$c] == '/' ) {
					$c += 1;
					$this->T->new_tag_formation( $this->s1 );
					$this->sR = '';
					$this->w = 111;
				} else if( $m[$c] == '<' ) {
					$this->T->text( '<' . $this->s1 );
					$c += 1;
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$this->T->text( '<' . $this->s1 );
					$c += 1;
					$this->w = 1;
				} else {
					$this->T->text( '<' . $this->s1 . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 111: // <a/
				if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->complete_tag( 1 );
					$this->w = 0;
				} else if( $m[$c] == '<' ) {
					$c += 1;
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 . '/' );
					$this->w = 10;
				} else if( $m[$c] == '&' ) {
					$c += 1;
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 . '/' );
					$this->w = 1;
				} else {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 . '/' . $m[$c] );
					$c += 1;
					$this->w = 0;
				}
				break;
			case 12: // <a.
				if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
				} else if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->sR .= $m[$c];
					$this->s2 = $m[$c];
					$c += 1;
					$this->w = 13;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->complete_tag(0);
					$this->w = 0;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 131;
				} else {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				}
				break;
			case 13: // <a.n
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->sR .= $m[$c];
					$this->s2 .= $m[$c];
					$c += 1;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 14;
				} else if( $m[$c] == '=' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 15;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, '' );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, '' );
					$this->w = 131;
				} else {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				}
				break;
			case 131: // <a.n/
				if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->complete_tag( 1 );
					$this->w = 0;
				} else {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				}
				break;
			case 14: // <a.n.
				if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
				} else if( $m[$c] == '=' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 15;
				} else if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->sR .= $m[$c];
					$this->T->tagattr( $this->s2, '' );
					$this->s2 = $m[$c];
					$c += 1;
					$this->w = 13;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, '' );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, '' );
					$this->w = 131;
				} else {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				}
				break;
			case 15: // <a.n= <a.n.=
				if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
				} else if( $m[$c] == '"' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 = '';
					$this->w = 22;
				} else if( $m[$c] == '\'' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 = '';
					$this->w = 27;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 = '';
					$this->w = 17;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 = '';
					$this->w = 21;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, '' );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else {
					$this->sR .= $m[$c];
					$this->s3 = $m[$c];
					$c += 1;
					$this->w = 16;
				}
				break;
			case 16: // <a.n=bv
				if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$this->T->tagattr( $this->s2, $this->s3 );
					$c += 1;
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 17;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 21;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= $m[$c];
					$c += 1;
				}
				break;
			case 17: // <a.n=bv&
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->sR .= $m[$c];
					$this->s4 = $m[$c];
					$c += 1;
					$this->w = 18;
				} else if( $m[$c] == '#' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 19;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&';
					$c += 1;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&';
					$this->w = 21;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $m[$c];
					$c += 1;
					$this->w = 16;
				}
				break;
			case 18: // <a.n=bv&a
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->sR .= $m[$c];
					$this->s4 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= $this->T->attrent( $this->s4 );
					$this->w = 16;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' . $this->s4 );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $this->s4;
					$c += 1;
					$this->w = 17;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&' . $this->s4;
					$this->w = 21;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' . $this->s4 );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $this->s4 . $m[$c];
					$c += 1;
					$this->w = 16;
				}
				break;
			case 19: // <a.n=bv&#
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->sR .= $m[$c];
					$this->s4 = $m[$c];
					$c += 1;
					$this->w = 20;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&#' );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#';
					$this->w = 17;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#';
					$this->w = 21;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&#' );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $m[$c];
					$c += 1;
					$this->w = 16;
				}
				break;
			case 20: // <a.n=bv&#3
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->sR .= $m[$c];
					$this->s4 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= mb_ord( $this->s4 );
					$this->w = 16;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3, '&#' . $this->s4 );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#' . $this->s4;
					$this->w = 17;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $this->s4;
					$c += 1;
					$this->w = 21;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&#' . $this->s4 );
					$this->T->complete_tag( 0 );
					$this->w = 0;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $this->s4 . $m[$c];
					$c += 1;
					$this->w = 16;
				}
				break;
			case 21: // <a.n=bv/
				if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 );
					$this->T->complete_tag( 1 );
					$this->w = 0;
				} else if( $this->sp_tab_nl( $m[$c] ) ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '/' );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '/';
					$this->w = 17;
				} else if( $m[$c] == '<' ) {
					$this->T->canceltag();
					$this->T->text( '<' . $this->s1 );
					$this->sR .= substr( $m, $c );
					return 2;
				} else if( $m[$c] == '/' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '/';
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '/' . $m[$c];
					$c += 1;
					$this->w = 16;
				}
				break;
			case 22: // <a.n="
				if( $m[$c] == '"' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 23;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= $m[$c];
					$c += 1;
				}
				break;
			case 23: // <a.n="bc&
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->sR .= $m[$c];
					$this->s4 = $m[$c];
					$c += 1;
					$this->w = 24;
				} else if( $m[$c] == '#' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 25;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&';
					$c += 1;
				} else if( $m[$c] == '"' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $m[$c];
					$c += 1;
					$this->w = 22;
				}
				break;
			case 24: // <a.n="bc&a
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->sR .= $m[$c];
					$this->s4 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= $this->T->attrent( $this->s4 );
					$this->w = 22;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $this->s4;
					$c += 1;
					$this->w = 23;
				} else if( $m[$c] == '"' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' . $this->s4 );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $this->s4 . $m[$c];
					$c += 1;
					$this->w = 22;
				}
				break;
			case 25: // <a.n="bc&#
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->sR .= $m[$c];
					$this->s4 = $m[$c];
					$c += 1;
					$this->w = 26;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#';
					$this->w = 23;
				} else if( $m[$c] == '"' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3, '&#' );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $m[$c];
					$c += 1;
					$this->w = 22;
				}
				break;
			case 26: // <a.n="bc&#3
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->sR .= $m[$c];
					$this->s4 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= mb_ord( $this->s4 );
					$this->w = 22;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#' . $this->s4;
					$this->w = 23;
				} else if( $m[$c] == '"' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3, '&#' . $this->s4 );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $this->s4 . $m[$c];
					$c += 1;
					$this->w = 22;
				}
				break;
			case 27: // <a.n='
				if( $m[$c] == '\'' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 );
					$this->w = 12;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 28;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= $m[$c];
					$c += 1;
				}
				break;
			case 28: // <a.n='bc&
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ) {
					$this->sR .= $m[$c];
					$this->s4 = $m[$c];
					$c += 1;
					$this->w = 29;
				} else if( $m[$c] == '#' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->w = 30;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&';
					$c += 1;
				} else if( $m[$c] == '\'' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $m[$c];
					$c += 1;
					$this->w = 27;
				}
				break;
			case 29: // <a.n='bc&a
				if( $m[$c] >= 'a' && $m[$c] <= 'z' || $m[$c] >= 'A' && $m[$c] <= 'Z' || $m[$c] == '_' ||
						$m[$c] >= '0' && $m[$c] <= '9' || $m[$c] == '.' || $m[$c] == ':' || $m[$c] == '-' ) {
					$this->sR .= $m[$c];
					$this->s4 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= $this->T->attrent( $this->s4 );
					$this->w = 27;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $this->s4;
					$c += 1;
					$this->w = 28;
				} else if( $m[$c] == '\'' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3 . '&' . $this->s4 );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&' . $this->s4 . $m[$c];
					$c += 1;
					$this->w = 27;
				}
				break;
			case 30: // <a.n='bc&#
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->sR .= $m[$c];
					$this->s4 = $m[$c];
					$c += 1;
					$this->w = 31;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#';
					$this->w = 28;
				} else if( $m[$c] == '\'' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3, '&#' );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $m[$c];
					$c += 1;
					$this->w = 27;
				}
				break;
			case 31: // <a.n='bc&#3
				if( $m[$c] >= '0' && $m[$c] <= '9' ) {
					$this->sR .= $m[$c];
					$this->s4 .= $m[$c];
					$c += 1;
				} else if( $m[$c] == ';' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= mb_ord( $this->s4 );
					$this->w = 27;
				} else if( $m[$c] == '&' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->s3 .= '&#' . $this->s4;
					$this->w = 28;
				} else if( $m[$c] == '\'' ) {
					$this->sR .= $m[$c];
					$c += 1;
					$this->T->tagattr( $this->s2, $this->s3, '&#' . $this->s4 );
					$this->w = 12;
				} else {
					$this->sR .= $m[$c];
					$this->s3 .= '&#' . $this->s4 . $m[$c];
					$c += 1;
					$this->w = 27;
				}
				break;
			case 40: // <! <!m <!--.--
				if( $m[$c] == '-' ) {
					$c += 1;
					$this->w = 41;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->complete_dt( $this->s1, $this->s2 );
					$this->w = 0;
				} else {
					$this->s1 .= $m[$c];
					$c += 1;
				}
				break;
			case 41: // <!-
				if( $m[$c] == '-' ) {
					$c += 1;
					$this->w = 42;
				} else if( $m[$c] == '>' ) {
					$c += 1;
					$this->T->complete_dt( $this->s1 . '-', $this->s2 );
					$this->w = 0;
				} else {
					$this->s1 .= '-' . $m[$c];
					$c += 1;
					$this->w = 40;
				}
				break;
			case 42: // <!--
				if( $m[$c] == '-' ) {
					$c += 1;
					$this->w = 43;
				} else {
					$this->s2 .= $m[$c];
					$c += 1;
				}
				break;
			case 43: // <!--- <!--.-
				if( $m[$c] == '-' ) {
					$c += 1;
					$this->w = 40;
				} else {
					$this->s2 .= '-' . $m[$c];
					$c += 1;
					$this->w = 42;
				}
				break;
			}
		}
	}
	
	function priv_process_eof_sub() {
		switch( $this->w ) {
			case 0:
				break;
			case 1:
				$this->T->text( '&' );
				break;
			case 2:
				$this->T->text( '&' . $this->s1 );
				break;
			case 3:
				$this->T->text( '&#' );
				break;
			case 4: // &#0
				$this->T->text( '&#' . $this->s1 );
				break;
			case 10: // <
				$this->T->text( '<' );
				break;
			case 101: // </
				$this->T->text( '</' );
				break;
			case 102: // </a
				$this->T->text( '</' . $this->s1 );
				break;
			case 103: // </a.
				$this->T->text( '</' . $this->s1 . $this->sR );
				break;
			case 11: // <a
				$this->T->text( '<' . $this->s1 );
				break;
			case 111: // <a/
				$this->T->text( '<' . $this->s1 . '/' );
				break;
			case 12: // <a.
			case 13: // <a.n
			case 131: // <a.n/
			case 14: // <a.n.
			case 15: // <a.n= <a.n.=
			case 16: // <a.n=bv
			case 17: // <a.n=bv&
			case 18: // <a.n=bv&a
			case 19: // <a.n=bv&#
			case 20: // <a.n=bv&#3
			case 21:
			case 22: // <a.n="
			case 23: // <a.n="bc&
			case 24: // <a.n="bc&a
			case 25: // <a.n="bc&#
			case 26: // <a.n="bc&#3
			case 27: // <a.n='
			case 28: // <a.n='bc&
			case 29: // <a.n='bc&a
			case 30: // <a.n='bc&#
			case 31: // <a.n='bc&#3
				$this->T->canceltag();
				$this->T->text( '<' . $this->s1 );
				return 2;
			case 40: // <! <!m <!--.--
			case 41: // <!-
			case 42: // <!--
			case 43: // <!--- <!--.-
				$this->T->text( '<!' . $this->s1 . '--' . $this->s2 );
				break;
			}
	}
	function process_eof() {
		while( $this->priv_process_eof_sub( ) == 2 ) {
			do {
				$this->w = 0;
			} while( $this->priv_process_chunk_sub( $this->sR ) == 2 );
		}
	}
	function process_chunk($m) {
		if( $this->priv_process_chunk_sub( $m ) == 2 ) {
			do {
				$this->w = 0;
			} while( $this->priv_process_chunk_sub( $this->sR ) == 2 );
		}
	}
}

?>