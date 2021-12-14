<?php

# This module provides send-mail functionality for egg-sgml.
#
# The simplest use of this is, for example:
# <module path="mail.php"><mail to=youraddress@domain.com from=webserver@domain.com subject="Web response">
# <html><body>Message received from <output name=name/>, <output name=email/>:
# <div><output name=message/></div></body></html>
# </mail><html><body><form method=post>Your name:<input.mail name=name/><br/>
# Your email:<input.mail name=email/><br/>
# Your message: <textarea.mail name=message/><br/>
# <input type=submit/><input type=hidden name=token value=1/></form>
# <ok.mail>Message sent successfully.</ok.mail>
# <error.mail>Please check your email address.</error.mail>
# </body></html></module>
#
# Notes: 
#  1. The first <html> subtree is the email message that will be sent.
#  2. <input type=hidden name=token value=1/> is used to ensure that the form is complete (rare circumstance of half-shown forms avoided).
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
	public $NF, $env;
	function __construct($env) {
		$this->env = $env;
	}
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
			if( $w->firstChild != null && ! array_key_exists( strtolower($w->nodeName), $this->env->sct ) ) {
				$q->write('</' . $w->nodeName . '>'); }
			return 1; }
		$q->write('<' . $w->nodeName);
		if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
			$q->write(' ' . $w->attributes->item($m)->name);
			if( $w->attributes->item($m)->value != null ) {
				$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
		}
		if( $w->firstChild == null ) {
			if( ! array_key_exists( strtolower($w->nodeName), $this->env->sct ) ) {
				$q->write('></' . $w->nodeName);
			} else { $q->write('/'); }
		}
		$q->write('>');
		return 2; }
};

class mtgc_mail__select {
	public $NF, $env, $validity, $form_elem;
	function __construct($form_elem,$validity,$env) {
		$this->validity = $validity;
		$this->env = $env;
		$this->form_elem_name = $form_elem;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		if( $w->nodeName == 'option' ) {
			if( $end ) {
				$q->write('</option>');
				return 1; }
			$q->write('<option');
			if( $this->validity == 1 ) {
				if( attribute_exists( $w, 'value' ) ) {
					if( $_POST[$this->form_elem_name] == $w->getAttribute('value') ) {
						$q->write(' selected'); 	}
				} else {
					if( $w->firstChild != null ) if( $w->firstChild->nodeType == 3 )
						if( $w->firstChild->data == $_POST[$this->form_elem_name] ) {
							$q->write(' selected'); }
				}
			}
			if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				if( $this->validity != 1 || 'selected' != $w->attributes->item($m)->name ) {
					$q->write(' ' . $w->attributes->item($m)->name);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				}
			}
			$q->write('>');
			return 2;
		}
		return 0; }
};

class tgc_module_mail {
	public $NF;
	public $path, $env;
	public $validity;
	public $suppress_required;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->validity = 0;
		$this->not_exist = array();
		$this->ok_format = array();
		$this->suppress_required = false;
		if( $this->env->shipyard ) if( array_key_exists('required',$_GET) ) if( $_GET['required'] == 0 )
			$this->suppress_required = true;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function check_exists($q) {
		if( ! array_key_exists($q,$_POST) ) {
			$this->not_exist[$q] = true;
			$this->validity = 1; return false; }
		if( $_POST[$q] == '' ) {
			$this->not_exist[$q] = true;
			$this->validity = 1; return false;	}
		return true;
	}
	function sp_tab_nl( $c ) {
		return $c == ' ' || $c == "\t" || $c == "\n" || $c == "\r";
	}
	function _atext( $n ) {
		if( $n >= 'a' && $n <= 'z' || $n >= 'A' && $n <= 'Z' || $n >= '0' && $n <= '9' ) {
			return true; }
		if( $n == '!' || $n == '#' || $n == '$' || $n == '%' || $n == '&' || $n == '\'' ||
				$n == '*' || $n == '+' || $n == '-' || $n == '/' || $n == '=' || $n == '?' ||
				$n == '^' || $n == '_' || $n == '`' || $n == '{' || $n == '|' || $n == '}' ||
				$n == '~' ) {
			return true; }
		return false; }
/*atext           =       ALPHA / DIGIT / ; Any character except controls,
                        "!" / "#" /     ;  SP, and specials.
                        "$" / "%" /     ;  Used for atoms
                        "&" / "'" /
                        "*" / "+" /
                        "-" / "/" /
                        "=" / "?" /
                        "^" / "_" /
                        "`" / "{" /
                        "|" / "}" /
                        "~" */
	function valid_email( $n ) {
		$m = $d = 00;
		for( $m = 0; $m < strlen($n); $m += 1 ) switch($d) {
		case 0:
			if( $this->sp_tab_nl( $n[$m] ) ) {
			} else if( $this->_atext($n[$m]) ) {
				$d = 1;
			} else {
				return false; }
			break;
		case 1:
			if( $this->_atext($n[$m]) ) {
			} else if( $n[$m] == '.' ) {
				$d = 2;
			} else if( $n[$m] == '@' ) {
				$d = 3;
			} else {
				return false; }
			break;
		case 2:
			if( $this->_atext($n[$m]) ) {
				$d = 1;
			} else return false;
			break;
		case 3:
			if( $this->_atext($n[$m]) ) {
				$d = 4;
			} else return false;
			break;
		case 4:
			if( $this->sp_tab_nl( $n[$m] ) ) {
				$d = 5;
			} else if( $this->_atext($n[$m]) ) {
			} else if( $n[$m] == '.' ) {
				$d = 3;
			} else return false;
			break;
		case 5:
			if( $this->sp_tab_nl( $n[$m] ) ) {
			} else return false;
			break;
		}
		switch($d) {
		case 0:
		case 1:
		case 2:
		case 3:
			return false;
		case 4:
			return true;
		case 5:
			return true;
		}
	}	
	function validate() {
		$this->validity = 2;
		$this->check_exists( 'name' );
		#$this->check_exists( 'surname' );
		if( $this->check_exists( 'email' ) ) {
			$this->ok_format['email'] = $this->valid_email( $_POST['email'] );
		}
		#$this->check_exists( 'need' );
		$this->check_exists( 'message' );
	}
	function add_record($connection_file) {
		$u = $m = 00;
		$m = file_get_contents($connection_file);	
		$u = pg_connect($m);
		pg_query_params( $u, "insert into query(name,email,message) values ($1,$2,$3);", array( $_POST['name'], $_POST['email'], $_POST['message'] ) );
	}
	function consume( $q, $end, $w ) {
		$k = $q1 = $m = 00;
		if( $w->nodeName == 'mail' ) {
			if( $end ) return 1;
			if( array_key_exists('token',$_POST) ) if( $_POST['token'] === '1' ) {
				$this->validate();
				if( $this->validity == 2 ) {
					$q1 = new buffer_out;
					$k = newframe(new tgc_mail($this->env), $q1 , $w);
					$k->P = null;
					eggsgml($k);
#					$this->add_record($w->getAttribute('pg_connection_file'));
					if( attribute_exists( $w, 'diag') ) {
						$q->write( sr_amp_lt( $q1->a ) );
					} else {
						mail( $w->getAttribute('to'), $w->getAttribute('subject'), $q1->a,
							array( 'From' => $w->getattribute('from'), 'Content-Type' => 'text/html' ) );
					}
				}
			}
			return 1;
		}
		if( $w->nodeName == 'input.mail' ) {
			if( $end ) return 1;
			$q->write( '<input' );
			if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				do {
					if( $this->validity == 1 ) 
						if( 'value' == $w->attributes->item($m)->name ) break;
					if( $this->suppress_required ) {
						if( 'required' == $w->attributes->item($m)->name )
							break;
						if( 'type' == $w->attributes->item($m)->name )
							if( 'email' == $w->attributes->item($m)->value ) {
								$q->write(' type="text"'); break; }
					}
					$q->write(' ' . $w->attributes->item($m)->name);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				} while(0);
			}
			if( $this->validity == 1 ) {
				$q->write(' value="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $_POST[$w->getAttribute('name')] ) ) . '"' );
			}
			$q->write('/>');
			return 1;
		}
		if( $w->nodeName == 'textarea.mail' ) {
			if( $end ) return 1;
			$q->write( '<textarea' );
			if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				do {
					if( $this->suppress_required )
						if( 'required' == $w->attributes->item($m)->name )
							break;
					$q->write(' ' . $w->attributes->item($m)->name);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				} while(0);
			}
			$q->write( '>' );
			if( $this->validity == 1 ) {
				$q->write( sr_amp_lt( $_POST[$w->getAttribute('name')] ) );
			} else {
				if( $w->firstChild != null && $w->firstChild->nodeType == 3 ) {
					$q->write( sr_amp_lt( $w->firstChild->data ) );
				}
			}
			$q->write( '</textarea>' );
			return 1;
		}
		if( $w->nodeName == 'error.exist' ) {
			if( $end ) return 1;
			if( array_key_exists( $w->getAttribute('name'), $this->not_exist ) )
				if( $this->not_exist[ $w->getAttribute('name') ] ) return 2;
			return 1;
		}
		if( $w->nodeName == 'error.format' ) {
			if( $end ) return 1;
			if( array_key_exists( $w->getAttribute('name'), $this->ok_format ) )
				if( ! $this->ok_format[ $w->getAttribute('name') ] ) return 2;
			return 1;
		}
		if( $w->nodeName == 'error.mail' ) {
			if( $end ) return 1;
			if( $this->validity == 1 ) return 2;
			return 1;
		}
		if( $w->nodeName == 'ok.mail' ) {
			if( $end ) return 1;
			if( $this->validity == 2 ) return 2;
			return 1;
		}
		if( $w->nodeName == 'select.mail' ) {
			if( $end ) {
				$q->write('</select>');
				return 1; }
			$this->NF = newframe( new mtgc_mail__select($w->getAttribute('name'),$this->validity,$this->env), $q, $w );
			$q->write('<select');
			if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				do {
					if( $this->suppress_required )
						if( 'required' == $w->attributes->item($m)->name )
							break;
					$q->write(' ' . $w->attributes->item($m)->name);
					if( $w->attributes->item($m)->value != null ) {
						$q->write('="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $w->attributes->item($m)->value ) ) . '"' ); }
				} while(0);
			}
			$q->write('>');
			return 3;
		}
		return 0;
	}
};

return new tgc_module_mail;