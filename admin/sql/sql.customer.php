<?php
/**
 * 		Datei: 					admin/sql/sql.customer.php
 * 		Erstellungsdatum:		25.12.2010
 * 		Letzte bearbeitung:		14.10.2011
 * 		Beschreibung:			SQL Für customer modul
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

$query = array();

$query[] = "CREATE TABLE `".$prefix."customer` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`anrede` VARCHAR( 250 ) NOT NULL ,
`vorname` VARCHAR( 250 ) NOT NULL ,
`nachname` VARCHAR( 250 ) NOT NULL ,
`firma` VARCHAR( 250 ) NOT NULL ,
`zusatzzeile` VARCHAR( 250 ) NOT NULL ,
`plz` INT( 10 ) NOT NULL ,
`ort` VARCHAR( 250 ) NOT NULL ,
`land` VARCHAR( 250 ) NOT NULL ,
`telefonnummer` VARCHAR( 250 ) NOT NULL ,
`mobiltelefonnummer` VARCHAR( 250 ) NOT NULL ,
`email` VARCHAR( 250 ) NOT NULL ,
`notizen` TEXT NOT NULL ,
`kundennummer` INT( 10 ) NOT NULL ,
`date` INT( 15 ) NOT NULL ,
`strasse` VARCHAR( 250 ) NOT NULL
) ENGINE = MYISAM ;";

$mysql->select('id', $prefix.'modul_cat', 'WHERE name = '.sql('Markt'));
if ($mysql->count() == 0) {
	$mysql->insert(array('name' => sql('Markt')), $prefix.'modul_cat');
}

$mysql->select('id', $prefix.'admin_navigation_cat', 'WHERE name = '.sql('Markt'));
if ($mysql->count() == 0) {
	$mysql->insert(array('name' => sql('Markt')), $prefix.'admin_navigation_cat');
}

?>