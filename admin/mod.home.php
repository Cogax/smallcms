<?php
/**
 * 		Datei: 					admin/mod.home.php
 * 		Erstellungsdatum:		25.07.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Startseite
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

// Erstellen links
$link = icon_link('Neuen Artikel erstellen', 'article-add', 'index.php?modul=article&action=add');
$link .= icon_link('Neue Seite erstellen', 'file-add', 'index.php?modul=page&action=add');
$link .= icon_link('Neues Template', 'image-add-alt', 'index.php?modul=template&action=add');
$style->box('Optionen', $link, 'left');

// Left Text
$text_left = 'Bald haben wir auch eine Dokumentation über unser smallCMS. Wir bitten Sie um verständniss.';
$style->box('Dokumentation & FAQ',$text_left, 'left');

// Content Text
$text_right = 'Herzlich Willkommen im Administrationsbereich Ihres smallCMS. SmallCMS befindet sich zur Zeit noch in Aufbauphase
und ist darum bei Ihnen erst in der Beta Version freigeschalten.<br />
Falls Sie einen Fehler finden, schreiben Sie uns bitte eine Email auf info@cogax.ch. Vielen Dank!<br /> <br />
<a href="http://www.cogax.ch/">Cogax.ch</a><br /><br />';
$style->box('Willkommen!',$text_right);

// System Info's
$mysql->select('*', $prefix.'system_info', 'ORDER BY version DESC LIMIT 1');
$system_info = $mysql->fetchRow();

$list = new liste('Aktuelle Systeminformationen');
$list->add_columns(array('' => '50%', ' ' => ''));
$list->add_row(array(liste_title('Systemname'), $system_info['name']));
$list->add_row(array(liste_title('Version'), $system_info['version']));
$list->add_row(array(liste_title('Status'), $system_info['version_status']));
$list->add_row(array(liste_title('Letzte Aktualisierung'), date("d.m.Y", $system_info['date'])));
$list->add_row(array(liste_title('Author'), $system_info['author']));
$list->add_row(array(liste_title('Copyright'), $system_info['copyright']));
$style->add($list->get());
?>