<?php
#    templates.php - Egg SGML
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

include 'eggsgml.php';
include 'eggsgml-loader.php';
include 'origin.php';
include 'tgc_generic.php';

function main($doc,$self_href) {
	$d = load_eggsgml_file( $doc );
	$k = newframe(new tgc_generic(dirname($doc,1),$self_href), new echo_out, $d);
	$k->P = null;
	test($k);
}

function check_shipyard_auth($u) {
	if( ! file_exists($u . "/shipyard.txt") ) return true;
	$m = file_get_contents($u . '/shipyard.txt');
	if( $m === false ) return true;
	if( array_key_exists( 'shipyard', $_COOKIE ) ) {
		if( $_COOKIE['shipyard'] === $m ) {
			return true; } }
	if( array_key_exists( 'shipyard', $_GET ) ) {
		if( $_GET['shipyard'] === $m ) {
			setcookie('shipyard',$m);
			return true; } }
}

function metamain() {
	$u = 00;
	if( array_key_exists( 'c', $_GET ) ) {
		$u = '..'; } else { $u = '../..'; }
	if( $_SERVER['HTTPS'] ) {
		header( 'Strict-Transport-Security: max-age=333300; includeSubDomains; preload' );
		if( ! check_shipyard_auth($u) ) {
			main( $u .'/shipyard', '/shipyard');
			return; }
		if( $_GET['t'] == '/' ) {
			main($u .'/index', $_GET['t']);
		} else if( $_GET['t'] == '/index' ) {
			header('Location:https://' . $_SERVER['HTTP_HOST'] . '/' );
		} else {
			main($u . $_GET['t'], $_GET['t'] );
		}
	} else {
		header('Location:https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}
}

metamain();

?>