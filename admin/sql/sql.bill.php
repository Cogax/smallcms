<?php
/**
 * 		Datei: 					admin/sql/sql.z_bill.php
 * 		Erstellungsdatum:		28.12.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			SQL Für bill module
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

$query = array();

$query[] = "CREATE TABLE `".$prefix."bill` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`customer` INT( 10 ) NOT NULL ,
`kundennummer` INT( 15 ) NOT NULL ,
`rechnungsnummer` VARCHAR( 50 ) NOT NULL ,
`product_data` TEXT NOT NULL ,
`date` INT( 15 ) NOT NULL ,
`referenznummer` INT( 50 ) NOT NULL ,
`zahlungszweck` VARCHAR( 250 ) NOT NULL ,
`notizen` TEXT NOT NULL,
`status` INT( 10 ) NOT NULL ,
`rechnungsadresse` TEXT NOT NULL,
`template` INT( 10 ) NOT NULL,
`betrag` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM ;
";

$query[] = "CREATE TABLE `".$prefix."bill_template` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 250 ) NOT NULL ,
`path` VARCHAR( 250 ) NOT NULL ,
`date` INT( 15 ) NOT NULL
) ENGINE = MYISAM ;";

$query[] = "INSERT INTO `".$prefix."option_cat` (
`id` ,
`name`
)
VALUES (
NULL , 'Rechnungsverwaltung'
);";

$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'bank_address', '', 'Adresse der bank (Einzahlungsschein)', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'own_address', '', 'Eigene Adresse (Einzahlungsschein)', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'account_nr', '', 'Kontonummer', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'iban', '', 'Iban', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'zahlungsfrist1', '', 'Zahlungsfrist Rechnung in tagen', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'zahlungsfrist2', '', 'Zahlungsfrist Mahnung 1 in tagen', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'zahlungsfrist3', '', 'Zahlungsfrist Mahnung 2 in tagen', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
)
)";
$query[] = "INSERT INTO `".$prefix."option` ( name, value, description, cat )
VALUES (
'zahlungsfrist4', '', 'Zahlungsfrist Mahnung 3 in tagen', (
SELECT id
FROM ".$prefix."option_cat
WHERE name = 'Rechnungsverwaltung'
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
	$insert = array();
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
	$mod_id = $mysql->insert_id();
	
	$update = array();
	$update['start_modul'] = sql($mod_id);
	$mysql->update($update, $prefix.'admin_navigation_cat', sql($nc_id));
}
?>