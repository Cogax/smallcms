<?php
/**
 * 		Datei: 					index.php
 * 		Erstellungsdatum:		28.07.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Hauptseite
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);

// Prüfen ob fertig installiert
if (is_dir('install/')) die('Die Installation wurde noch nicht vollständig abgeschlossen!');

// Dateien einbinden
require_once('conf.config.php');
require_once('class.mysql.php');
require_once('func.functions.php');

// MySQL Verbindung starten
$mysql = new mysql($host, $database, $user, $pass, 'on');

// Seite bestimmen
if(isset($_GET[option('parameter')])) {
	$page_id = $_GET[option('parameter')];
} else {
	$page_id = option('start_page');
}

// Seite laden
$mysql->id_select('*', $prefix.'page', sql($page_id));
if ($mysql->count() == 0) die('Error!');
$page = $mysql->fetchRow();
$data = unserialize($page['data']);

// Template laden
$mysql->id_select('*', $prefix.'template', sql($page['template']));
$template = $mysql->fetchRow();
$tpl = file_get_contents('templates/'.$template['path']);

// Platzhalter durchgehen
foreach (unserialize($template['platzhalter']) as $key => $platzhalter) {
	// Artikel nach reienfolge sortieren
	ksort($data[$platzhalter]);
	
	$output = '';
	foreach ($data[$platzhalter] as $sort => $article_id) {
		$mysql->id_select('*', $prefix.'article', sql($article_id));
		$article = $mysql->fetchRow();
		
		// Text oder Script
		if($article['type'] == '0') {
			// Text
			$output .= $article['text'];
		} else {
			// Script
			ob_start();
			include('scripts/'.$article['path']);
			$output .= ob_get_contents();
			ob_clean();
		}
	}
	
	// Platzhalter parsen
	$tpl = str_replace('{'.option('ph_start').'}'.$platzhalter.'{'.option('ph_end').'}', $output, $tpl);
}

// Ausgabe
echo $tpl;

// MySQL Verbindung schliessen
$mysql->disconnect();
?>