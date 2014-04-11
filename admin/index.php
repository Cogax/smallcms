<?php
/**
 * 		Datei: 					admin/index.php	
 * 		Erstellungsdatum:		25.07.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Backend Index
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);

// Prüfen ob fertig installiert
if (is_dir('install/')) die('Die Installation wurde noch nicht vollständig abgeschlossen!');

// Variabel definieren
define('ADMIN', true);

// Plugins
require_once('../plugins/phplivex/PHPLiveX.php'); // PHPLiveX Ajax Framework: http://www.phplivex.com/example/sending-ajax-request-to-pages

// Dateien einbinden
require_once('../conf.config.php');
require_once('../class.mysql.php');
require_once('class/class.style.php');
require_once('class/class.liste.php');
require_once('class/class.form.php');
require_once('class/class.tree.php');
require_once('class/class.ajax.php');
require_once('func/func.functions.php');

// MySQL Verbindung starten
$mysql = new mysql($host, $database, $user, $pass);
$mysql_func = new mysql($host, $database, $user, $pass);

// Style
$style = new style('smallCMS BETA');


// Navigationlinks
$nlinks = array();
$res = $mysql->select('name, start_modul', $prefix.'admin_navigation_cat');
while ($cat = $mysql->fetchRow($res)) {
	$nlinks['index.php?modul='.$mysql->get_from_id('mod_link', $prefix.'modul', $cat['start_modul'])] = $cat['name'];
}
$style->navbar($nlinks);

// Falls kein Modul angefortert oder nicht existiert -> Home
$mysql->select('id, mod_file', $prefix.'modul', 'WHERE mod_link = '.sql(@$_GET['modul']));
$mod = $mysql->fetchRow();
if (!isset($_GET['modul']) || $mysql->count() == 0) {
	// Kein Modul gewählt oder Modul nicht gefunden -> Home
	$_GET['modul'] = 'home';
	$mod['mod_file'] = 'mod.home.php';
}

// Modul laden
define('INDEX', 'index.php?modul='.$_GET['modul']);
include_once($mod['mod_file']);

// HTML Ausgabe (Style)
echo $style->get();

// MySQL Verbindung schliessen
$mysql->disconnect();
$mysql_func->disconnect();
?>