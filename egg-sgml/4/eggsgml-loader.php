<?php
#    eggsgml-loader.php - Use Egg SGML parser (contrast with php-loader using XML parser).
#    Copyright (C) 2021 Brian Jonnes

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

function load_eggsgml_file( $doc ) {
	$k = $n = $u = $m = 00;
	
	$m = new W3CDOM_tagreceiver();
	$u = new eggsgml_parser($m);
	$k = fopen($doc,'r');
	while(1) {
		$n = fread( $k, 77 );
		if( $n == '' ) break;
		$u->process_chunk( $n );
	}
	$u->process_eof();
	return $m->w;
}

?>