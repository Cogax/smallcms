<?php
/**
 * 		Datei: 					admin/info/info.testmodul.php
 * 		Erstellungsdatum:		05.09.2011
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Beispiel f�r Infodatei
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

$info = array(
	// ----- Modul Info's
	'mod_name' 						=> 'Testmodul',							// Modulname
	'mod_key'						=> 'test',								// Modulkey	
	'mod_link'						=> 'test',								// Modul Linkparameter
	'mod_archive'					=> 'testmodul_1.0.zip',					// Archivname des Moduls
	'mod_version' 					=> '1.0',								// Version
	'mod_author' 					=> 'Andreas Gyr',						// Autor
	'mod_copyright' 				=> 'Andreas Gyr',						// Kopierrechte
	'mod_date'						=> '05.09.2011',						// Ver�ffentlichungsdatum
	'mod_changelog' 				=> '-',									// Changelog
	'mod_description' 				=> 'Testmodul',							// Modulbeschreibung
	'mod_file'						=> 'admin/mod.testmod.php', 			// Moduldatei
	
	// ----- Dazugeh�rige Info's
	'sql_file'						=> 'admin/sql/sql.testmodul.php', 		// SQL Dateien
	'mod_need'						=> '0', 								// Ben�tigte Module
	
	// ----- Modul Zuordnung
	'mod_cat'						=> 'Testmodulkategorie',				// Name der Modulkategorie
	'navigation_cat'				=> 'Einstellungen',						// Name der Navigationskategorie
	'navigation_show'				=> '1',									// Soll in der Navigation angezeigt werden? 1 : 0
	
	// ----- Modul Icon's
	'icon_small'					=> 'arrow-right.png',					// Small Icon 
	'icon_big'						=> 'applications-accessories-3.png'		// Big Icon (Muss in Archiv mitgeliefert werden!)
);
?>