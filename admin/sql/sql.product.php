<?php
/**
 * 		Datei: 					admin/sql/sql.z_product.php
 * 		Erstellungsdatum:		27.12.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			SQL Für product module
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

$query = array();

$query[] = "CREATE TABLE `".$prefix."product_cat` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 250 ) NOT NULL
) ENGINE = MYISAM ;";

$query[] = "CREATE TABLE `".$prefix."product` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 250 ) NOT NULL ,
`price` VARCHAR( 100 ) NOT NULL ,
`cat` INT( 10 ) NOT NULL ,
`picture` VARCHAR( 250 ) NOT NULL ,
`thumbnail` VARCHAR( 250 ) NOT NULL ,
`description` TEXT NOT NULL ,
`status` VARCHAR( 10 ) NOT NULL ,
`date` INT( 15 ) NOT NULL
) ENGINE = MYISAM ;";

$query[] = "INSERT INTO `".$prefix."option_cat` (
`id` ,
`name`
)
VALUES (
NULL , 'Produktverwaltung'
);";

$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'currency', '.- CHF', 'Währung', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Produktverwaltung'
)
)";

$mysql->select('id', $prefix.'modul_cat', 'WHERE name = '.sql('Markt'));
if ($mysql->count() == 0) {
	$mysql->insert(array('name' => sql('Markt')), $prefix.'modul_cat');
}

$mysql->select('id', $prefix.'admin_navigation_cat', 'WHERE name = '.sql('Markt'));
$nc = $mysql->fetchRow();
$nc_id = $nc['id'];
if ($mysql->count() == 0) {
	$mysql->insert(array('name' => sql('Markt')), $prefix.'admin_navigation_cat');
	$nc_id = $mysql->insert_id();
}

$mysql->select('id', $prefix.'modul', 'WHERE mod_key = '.sql('start_markt'));
if ($mysql->count() == 0) {
	$insert['mod_name'] 			= sql('Markt Übersicht');
	$insert['mod_key'] 				= sql('start_markt');
	$insert['mod_link'] 			= sql('start_markt');
	$insert['mod_archive'] 			= sql('0');
	$insert['mod_version'] 			= sql('1.0');
	$insert['mod_author'] 			= sql('Andreas Gyr');
	$insert['mod_copyright'] 		= sql('Andreas Gyr');
	$insert['mod_date'] 			= sql('1316725322');
	$insert['mod_changelog'] 		= sql('-');
	$insert['mod_description'] 		= sql('Startseite für Markt Navigationskategorie');
	$insert['mod_file'] 			= sql('start.markt.php');
	$insert['sql_file'] 			= sql('0');
	$insert['mod_need'] 			= sql('0');
	$insert['mod_cat'] 				= sql('2'); // Startseiten für Navigationskategorien
	$insert['navigation_cat'] 		= sql($nc_id);
	$insert['navigation_show'] 		= sql('0');
	$insert['icon_small'] 			= sql('');
	$insert['icon_big'] 			= sql('');
	$mysql->insert($insert, $prefix.'modul');
}
?>