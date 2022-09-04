<?php

class tgc_mailrecaptcha_secrets {
	public $NF, $env;
	public $grecaptcha_secret;
	function __construct($env) {
		$this->env = $env;
		$this->grecapture_secret = '';
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch( $w->nodeName ) {
		case 'grecaptcha':
			if( $end ) return 1;
			$this->grecaptcha_secret = $w->getAttribute('secret');
			return 2;
		}
		return 0;
	}
}

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

class mailegg {
	public $tsq, $tsr, $tsw;
	public $writernode;
	public $q;
	function __construct($w,$environ) {
		$this->q = new buffer_out;
		$this->w = $w;
		$this->tgc = new tgc_mail($environ);
	}
	function write($a) {
		$this->q->write($a); 
	}
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
	function tap( $env, $str ) {
		$c = null;
		if( $str ) {
			mail( $this->w->getAttribute('to'), $this->w->getAttribute('subject'), $this->q->a,
			   array( 'From' => $this->w->getattribute('from'), 'Content-Type' => 'text/html' ) );
			return;
		}
		$env->enqueue_surreal( $this, [ 'earring', 'tgc' ] );
		sane_enqueue_subtree( $env, $this->w, '' );
	}
};


class tgc_proven {
	public $NF;
	public $path, $env;
	public $clipname;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$a = ''; $c = $k = null;
		switch($w->nodeName) {
		case 'mail':
			if( $end ) return 1; // retained for consistency
			$this->NF = new mailegg( $w, $this->env );
			return 4; // bypasses tgc on $end
		//
		//	$q1 = new buffer_out;
		//	$k = newframe(new tgc_mail($this->env), $q1 , $w);
		//	$k->P = null;
		//	eggsgml($k);
#					$this->add_record($w->getAttribute('pg_connection_file'));
		//	if( attribute_exists( $w, 'diag') ) {
		//		$q->write( sr_amp_lt( $q1->a ) );
		//	} else {
		//		mail( $w->getAttribute('to'), $w->getAttribute('subject'), $q1->a,
		//			array( 'From' => $w->getattribute('from'), 'Content-Type' => 'text/html' ) );
		//	}
		//	return 1;
		}
	}
}

class tgc_mail__select {
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


class tgc_response_test {
	public $NF;
	public $path, $env;
	public $clipname;
	function __construct($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->validity = 0;
		$this->not_exist = array();
		$this->ok_format = array();
	}
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
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		switch($w->nodeName) {
		case 'proven':
			if( $end ) return 1;
			$this->validate();
			if( $this->validity == 2 ) {
				$this->NF = newframe( new tgc_proven($this->path,$this->env), $q, $w );
				return 3; }
			return 1;
		}
	}
}

class mtgc_mailrecaptcha {
	public $NF;
	public $path, $env;
	public $clipname;
	public $response_test;
	public $secrets;
	function initialize($path,$env) {
		$this->path = $path;
		$this->env = $env;
		$this->response_test = new tgc_response_test($this->path,$this->env);
		$this->suppress_required = false;
		if( $this->env->shipyard ) if( array_key_exists('required',$_GET) ) if( $_GET['required'] == 0 )
			$this->suppress_required = true;
		$this->secrets = new tgc_mailrecaptcha_secrets($this->env);
		$this->load_config();
	}
	function load_config() {
		$m = ''; $k = null;
		$m = $_SERVER['DOCUMENT_ROOT'];
		$d = $this->env->load_eggsgml_file( $this->env->secrets_path . 'mailrecaptcha.xml' );
		if( ! $d ) return;
		$k = newframe( $this->secrets, new dev_null, $d);
		$k->P = null;
		eggsgml($this->env,$k,'');
	}
	function start( $q ) {
		return 0; }
	function repeat( $q ) {
		return 0; }
	function consume_text( $q, $x ) {
		$q->write(str_replace("<","&lt;", str_replace( "&", "&amp;", $x ) ) ); }
	function consume( $q, $end, $w ) {
		$c = null; $k = null; $a = '';
		switch($w->nodeName) {
		case 'response-test':
			if( $end ) return 1;
			if( array_key_exists('token',$_POST) ) if( $_POST['token'] === '1' ) {
				$this->NF = newframe( $this->response_test, $q, $w );
				return 3;
			}
			return 1;
		// start local
		case 'grecaptcha':
			if( $end ) return 1;
			// if( ! array_key_exists( 'g-recaptcha-response', $_POST ) ) {
			
			$a = 'secret=' . sr_25($this->secrets->grecaptcha_secret,'&') . '&response=' . sr_25($_POST['g-recaptcha-response'],'&');
			//$a = 'secret=asdf&response=asdf'; // &remoteip=
			$k = stream_context_create( [ 'http' => [ 'method' => 'POST',
			   'header' => 'Content-Type: application/x-www-form-urlencoded',
			   'content' => $a ] ] );

			$c = fopen('https://www.google.com/recaptcha/api/siteverify','r',false,$k);
			$a = json_decode( stream_get_contents($c), true );
			$c = 0;
			//$q->write( sr_amp_lt( var_export($a) ) );
			//$q->write( array_key_exists('success',$a) );
			if( $a['success'] )
				return 2;
			else $this->response_test->validity = 1;
			return 1;
		// stop local
		case 'input.mail':
			if( $end ) return 1;
			$q->write( '<input' );
			if( $w->attributes ) for( $m = 0; $m < $w->attributes->length; $m += 1 ) {
				do {
					if( $this->response_test->validity == 1 ) 
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
			if( $this->response_test->validity == 1 ) {
				$q->write(' value="' . str_replace('"',"&quot;", str_replace( "&", "&amp;", $_POST[$w->getAttribute('name')] ) ) . '"' );
			}
			$q->write('/>');
			return 1;
		case 'textarea.mail':
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
			if( $this->response_test->validity == 1 ) {
				$q->write( sr_amp_lt( $_POST[$w->getAttribute('name')] ) );
			} else {
				if( $w->firstChild != null && $w->firstChild->nodeType == 3 ) {
					$q->write( sr_amp_lt( $w->firstChild->data ) );
				}
			}
			$q->write( '</textarea>' );
			return 1;
		case 'error.exist':
			if( $end ) return 1;
			if( array_key_exists( $w->getAttribute('name'), $this->response_test->not_exist ) )
				if( $this->response_test->not_exist[ $w->getAttribute('name') ] ) return 2;
			return 1;
		case 'error.format':
			if( $end ) return 1;
			if( array_key_exists( $w->getAttribute('name'), $this->response_test->ok_format ) )
				if( ! $this->response_test->ok_format[ $w->getAttribute('name') ] ) return 2;
			return 1;
		case 'error.mail':
			if( $end ) return 1;
			if( $this->response_test->validity == 1 ) return 2;
			return 1;
		case 'ok.mail':
			if( $end ) return 1;
			if( $this->response_test->validity == 2 ) return 2;
			return 1;
		case 'select.mail':
			if( $end ) {
				$q->write('</select>');
				return 1; }
			$this->NF = newframe( new tgc_mail__select($w->getAttribute('name'),$this->response_test->validity,$this->env), $q, $w );
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
}

return new mtgc_mailrecaptcha;
?>
