<?php
/**
 * 		Datei: 					admin/info/info.bil.php
 * 		Erstellungsdatum:		05.09.2011
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Infodatei rechnungsverwaltung
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

$info = array(
	// ----- Modul Info's
	'mod_name' 						=> 'Rechnungen',						// Modulname
	'mod_key'						=> 'bill',								// Modulkey	
	'mod_link'						=> 'bill',								// Modul Linkparameter
	'mod_archive'					=> 'bill_1.0.zip',						// Archivname des Moduls
	'mod_version' 					=> '1.0',								// Version
	'mod_author' 					=> 'Andreas Gyr',						// Autor
	'mod_copyright' 				=> 'Andreas Gyr',						// Kopierrechte
	'mod_date'						=> '16.10.2011',						// Veröffentlichungsdatum
	'mod_changelog' 				=> '-',									// Changelog
	'mod_description' 				=> 'Rechnungsverwaltung',				// Modulbeschreibung
	'mod_file'						=> 'admin/mod.bill.php', 				// Moduldatei
	
	// ----- Dazugehörige Info's
	'sql_file'						=> 'admin/sql/sql.bill.php', 			// SQL Dateien
	'mod_need'						=> 'product, customer', 				// Benötigte Module
	
	// ----- Modul Zuordnung
	'mod_cat'						=> 'Markt',								// Name der Modulkategorie
	'navigation_cat'				=> 'Markt',								// Name der Navigationskategorie
	'navigation_show'				=> '1',									// Soll in der Navigation angezeigt werden? 1 : 0
	
	// ----- Modul Icon's
	'icon_small'					=> 'calendar.png',						// Small Icon 
	'icon_big'						=> 'applications-education.png'			// Big Icon (Muss in Archiv mitgeliefert werden!)
);
?>