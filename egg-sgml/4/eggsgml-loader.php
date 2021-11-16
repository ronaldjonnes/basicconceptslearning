<?php
#    eggsgml-loader.php - Use Egg SGML parser (contrast with php-loader using XML parser).
#    Copyright 2020, 2021 Brian Jonnes

#    Egg-SGML is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, version 3 of the License.

#    Egg-SGML is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.

#    You should have received a copy of the GNU General Public License
#    along with Egg-SGML.  If not, see <https://www.gnu.org/licenses/>.

function load_eggsgml_file( $doc ) {
	$k = $n = $u = $m = 00;
	
	$m = new W3CDOM_tagreceiver();
	$u = new eggsgml_parser($m);
	load_eggsgml_file_2( $m, $u, $doc );
	return $m->w;
}

function load_eggsgml_file_env( $env, $doc ) {
	$u = null;

	$m = new W3CDOM_tagreceiver();
	$u = new eggsgml_parser($m);
	if( $env->interimjsupgrade ) {
		$u->interimjsupgrade = true; }
	load_eggsgml_file_2( $m, $u, $doc );
	return $m->w;
}

?>