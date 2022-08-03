<?php

function enqueue_modules( $eggenv, $doc, $env ) {
	$k = null;
	$k = new tgc_egg( new tgc_generic(dirname($doc,1),$env) );
	$k->q = new echo_out;
	$eggenv->sane_enqueue( $k, 0, 1, [ 'tgc', 'earring' ] );
}

?>
