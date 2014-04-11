<?php
/**
 * 		Datei: 					admin/info/info.product.php
 * 		Erstellungsdatum:		13.10.2011
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Infodatei Produkte
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

$info = array(
	// ----- Modul Info's
	'mod_name' 						=> 'Produkte',							// Modulname
	'mod_key'						=> 'product',							// Modulkey	
	'mod_link'						=> 'product',							// Modul Linkparameter
	'mod_archive'					=> 'product_1.0.zip',					// Archivname des Moduls
	'mod_version' 					=> '1.0',								// Version
	'mod_author' 					=> 'Andreas Gyr',						// Autor
	'mod_copyright' 				=> 'Andreas Gyr',						// Kopierrechte
	'mod_date'						=> '13.10.2011',						// Veröffentlichungsdatum
	'mod_changelog' 				=> '-',									// Changelog
	'mod_description' 				=> 'Produkteverwaltung',				// Modulbeschreibung
	'mod_file'						=> 'admin/mod.product.php', 			// Moduldatei
	
	// ----- Dazugehörige Info's
	'sql_file'						=> 'admin/sql/sql.product.php', 		// SQL Dateien
	'mod_need'						=> '0', 								// Benötigte Module
	
	// ----- Modul Zuordnung
	'mod_cat'						=> 'Markt',								// Name der Modulkategorie
	'navigation_cat'				=> 'Markt',								// Name der Navigationskategorie
	'navigation_show'				=> '1',									// Soll in der Navigation angezeigt werden? 1 : 0
	
	// ----- Modul Icon's
	'icon_small'					=> 'product.png',						// Small Icon 
	'icon_big'						=> 'emblem-package.png'					// Big Icon (Muss in Archiv mitgeliefert werden!)
);
?>