<?php

function main() {
	$x = $n = $g = 00; $j = 0;

	$g = get_html_translation_table(HTML_ENTITIES);
	echo '$this->htmlent = [ ';
	foreach( $g as $x => $n ) {
		if( $n[0] == '&' && $n[strlen($n)-1] == ';' ) {
			if( $j ) echo ', '; $j = 1;
			echo '\'' . substr($n,1,strlen($n)-2) . '\' => ' . mb_ord($x);
		}
		
	}
	echo ' ];';
}
		
main()

?>