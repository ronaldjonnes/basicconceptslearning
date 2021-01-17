<?php

# This module provides send-mail functionality for egg-sgml.
#
# Firstly, we need the form data validated, if there was a submit request. We will do so
# when the <mail> tag is handled, and this must come as the first tag in the document (after <module>).
# The contents of <mail> will be the email message itself, which will include the tag <output>, replaced
# by the values entered in a corresponding <input>.
#
# The form validity ($validity) can be 0 - form not submitted, 1 - form submitted but errors, and 2 - form submitted and mail sent.
# If the form has not been submitted or the email has been sent, we show the <input> tags with the default value attribute;
# otherwise we include the submitted data.

# NOTE: output of tags needs to share tgc_generic.

class buffer_out {
	public $a;
	function write($b) {
		$this->a .= $b;
	}
};

class tgc_mail {
	public $NF;
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( $w->nodeName == 'output' ) {
			if( $end ) return 1;
			if( array_key_exists( $w->getAttribute('name'), $_POST ) ) {
				$q->write( str_replace('<','&lt;',str_replace('&','&amp;',$_POST[$w->getAttribute('name')])) ); }
			return 1;
		}
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
};

function attribute_exists( $w, $n ) {
	$m = 00;
	if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
		if( $w->attributes->item($m)->name == $n ) return true; }
	return false;
}

class tgc_module_root {
	public $NF;
	public $path, $self_href;
	public $validity;
	function __construct($path,$self_href) {
		$this->path = $path;
		$this->self_href = $self_href;
		$this->validity = 0;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$k = $q1 = $m = 00;
		if( $w->nodeName == 'mail' ) {
			if( $end ) return 1;
			if( array_key_exists('token',$_POST) ) if( $_POST['token'] === '1' ) {
				if( $_POST['email'] != '' ) {
					$this->validity = 2;
					$q1 = new buffer_out;
					$k = newframe(new tgc_mail, $q1 , $w);
					$k->P = null;
					test($k);
					if( attribute_exists( $w, 'diag') ) {
						$q->write( str_replace("<","&lt;", str_replace( "&", "&amp;", $q1->a ) ) );
					} else {
						mail( $w->getAttribute('to'), $w->getAttribute('subject'), $q1->a,
							array( 'From' => $w->getattribute('from'), 'Content-Type' => 'text/html' ) );
					}
				} else {
					$this->validity = 1;
				}
			}
			return 1;
		}
		if( $w->nodeName == 'input.mail' ) {
			if( $end ) return 1;
			$q->write( '<input' );
			if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				if( $this->validity != 1 || 'value' != $w->attributes->item($m)->name ) {
					$q->write(' ' . $w->attributes->item($m)->name);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				}
			}
			if( $this->validity == 1 ) {
				$q->write(' value="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $_POST[$w->getAttribute('name')] ) ) . '"' );
			}
			$q->write('/>');
			return 1;
		}
		if( $w->nodeName == 'error.mail' ) {
			if( $end ) return 1;
			if( $w->getAttribute('a') == 'email' ) {
				if( $this->validity == 1 ) return 2; }
			return 1;
		}
		if( $w->nodeName == 'ok.mail' ) {
			if( $end ) return 1;
			if( $this->validity == 2 ) return 2;
			return 1;
		}
		return 0;
	}
};
