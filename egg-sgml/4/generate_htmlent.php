<?php
#    generate_html_ent.php - Egg-SGML
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