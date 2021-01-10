<?php
include '2/eggsgml.php';

function main() {
	$k = $n = $u = $m = 00;
	
	$m = new diagnostic_tagreceiver();
	$u = new eggsgml_parser($m);
	$k = fopen('home','r');
	while(1) {
		$n = fread( $k, 77 );
		if( $n == '' ) break;
		$u->process_chunk( $n );
	}	
}

main();
?>