<?php
/**
 * 		Datei: 					admin/mod.article.php		
 * 		Erstellungsdatum:		25.07.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Artikelverwaltung
 * 		Autor:					Andreas Gyr
 */

error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

// Neun Artikel erstellen link
$link = icon_link('Übersicht', 'table', 'index.php?modul=article');
$link .= icon_link('Neuen Artikel', 'article-add', 'index.php?modul=article&action=add');
$link .= icon_link('Neue Seite', 'file-add', 'index.php?modul=page&action=add');
$style->box('Optionen', $link, 'left');

if(formular('add')) {
	// Neuen Artikel erstellen - Formular Step 1
	$form = new form('index.php?modul=article&action=add&step=2');
	$form->new_group('Schritt 1');
	$form->description('Es gibt zwei Typen von Artikel; "Text / HTML" und "Skript".<br />- Wollen Sie einen Text schreiben (mit Bildern etc.) dann
	wählen Sie <strong>"Text / HTML"</strong> aus.<br />- Wollen Sie ein PHP Skript einbinden so können Sie <strong>"Skript"</strong> wählen.');
	$form->select('Typ:', 'type', array('text' => 'Text / HTML', 'script' => 'Skript'));
	$form->submit('Weiter zu Schritt 2');
	$style->box('Neuen Artikel erstellen', $form->get());
} elseif (formular('add', 2)) {
	// Neuen Artikel erstellen - Formular Step 2
	if($_POST['type'] == 'text') {
		// Text Artikel erstellen: Type 0
		$form = new form('index.php?modul=article&action=add&type=text');
		$form->new_group('Schritt 2');
		$form->description('Sie können Ihrem Artikel nun einen Titel geben und den Inhalt schreiben. Den Editor kann in den Einstellungen geändert werden.');
		$form->input('Titel:', 'title', '', 'text', 50);
		$form->editor(option('editor'));
		$form->submit('Artikel erstellen');
		$style->box('Artikel erstellen', $form->get());
		// Js für Editor
		$style->editor_js(option('editor'));
	} else {
		// Script Artikel erstellen: Type 1
		$form = new form('index.php?modul=article&action=add&type=script');
		$form->new_group('Schritt 2');
		$form->description('Sie können nun eine Datei auswählen, welche im Verzeichnis "scripts/" abgelegt ist.');
		$form->input('Titel:', 'title', '', 'text', 50);
		
		// Scripts auslesen
		$options = array();
		$pfad = opendir(option('rel_path')."scripts/");
		while ($datei = readdir($pfad)) {
			if ($datei != '.' && $datei != '..' && !is_dir($pfad.$datei)) {
				$options[$datei] = $datei;
			}
		}
		closedir($pfad);
		$form->select('Skript:', 'path', $options);
		$form->submit('Artikel erstellen');
		$style->box('Artikel erstellen', $form->get());
	}
} elseif (entry('add')) {
	// Neuen Artikel erstellen - Eintrag
	$insert = array();
	$insert['title'] = sql($_POST['title']);
	$insert['template'] = "'0'";
	$insert['text'] = ($_GET['type'] == 'text' ? sql($_POST['editor1']) : sql('0'));
	$insert['date'] = sql(time());
	$insert['type'] = ($_GET['type'] == 'text' ? sql('0') : sql('1'));
	$insert['path'] = ($_GET['type'] == 'script' ? sql($_POST['path']) : sql('0'));
	$mysql->insert($insert, $prefix.'article');
	$style->box('Erfolgreich!',  p_icon().'Der Artikel wurde erfolgreich erstellt.'.back_overview());
} elseif (formular('edit')) {
	// Artikel bearbeiten - Formular STep 1
	$mysql->id_select('*', $prefix.'article', sql($_GET['id']));
	$article = $mysql->fetchRow();
	
	$form = new form('index.php?modul=article&action=edit&step=2&id='.$_GET['id']);
	$form->new_group('Schritt 1');
	$form->description('Es gibt zwei Typen von Artikel; "Text / HTML" und "Skript".<br />- Wollen Sie einen Text schreiben (mit Bildern etc.) dann
	wählen Sie <strong>"Text / HTML"</strong> aus.<br />- Wollen Sie ein PHP Skript einbinden so können Sie <strong>"Skript"</strong> wählen.');
	$selected = ($article['type'] == '0' ? 'text' : 'script');
	$form->select('Typ:', 'type', array('text' => 'Text / HTML', 'script' => 'Skript'), 1, false, $selected);
	$form->submit('Weiter zu Schritt 2');
	$style->box('Artikel bearbeiten', $form->get());
	
} elseif (formular('edit', 2)) {
	// Artikel bearbeiten - Formular Step 2
	
	// Artikel infos laden
	$mysql->id_select('*', $prefix.'article', sql($_GET['id']));
	$article = $mysql->fetchRow();
	
	if($_POST['type'] == 'text') {
		// Textartikel bearbeiten
		$form = new form('index.php?modul=article&action=edit&id='.$_GET['id'].'&type=text');
		$form->new_group('Schritt 2');
		$form->description('Sie können Ihrem Artikel nun einen Titel geben und den Inhalt schreiben. Den Editor kann in den Einstellungen geändert werden.');
		$form->input('Titel:', 'title', $article['title']);
		$value = ($article['text'] == '0' ? false : $article['text']);
		$form->editor(option('editor'), $value);
		$form->submit('Änderungen Speichern');
		$style->box('Artikel bearbeiten', $form->get());
		// Js für Editor
		$style->editor_js(option('editor'));
		
	} else {
		// Scriptartikel bearbeiten
		$form = new form('index.php?modul=article&action=edit&id='.$_GET['id'].'&type=script');
		$form->new_group('Schritt 2');		
		$form->description('Sie können nun eine Datei auswählen, welche im Verzeichnis "scripts/" abgelegt ist.');
		$form->input('Titel:', 'title', $article['title']);
		
		// Scripts auslesen
		$options = array();
		$pfad = opendir(option('rel_path')."scripts/");
		while ($datei = readdir($pfad)) {
			if ($datei != '.' && $datei != '..' && !is_dir($pfad.$datei)) {
				$options[$datei] = $datei;
			}
		}
		closedir($pfad);
		$selected = ($article['path'] == '0' ? false : $article['path']);
		$form->select('Skript:', 'path', $options, 1, false, $selected);
		$form->submit('Änderungen speichern');
		$style->box('Artikel bearbeiten', $form->get());
	}
	
} elseif (entry('edit')) {
	// Artikel bearbeiten - Eintrag
	$update = array();
	$update['title'] = sql($_POST['title']);
	$update['text'] = ($_GET['type'] == 'text' ? sql($_POST['editor1']) : sql('0'));
	$update['date'] = sql(time());
	$update['type'] = ($_GET['type'] == 'text' ? sql('0') : sql('1'));
	$update['path'] = ($_GET['type'] == 'script' ? sql($_POST['path']) : sql('0'));
	$mysql->update($update, $prefix.'article', sql($_GET['id']));
	$style->box(p_icon().'Bearbeitung erfolgreich!', 'Der Artikel konnte erfolgreich bearbeitet werden!'.back_overview());
} elseif (delete()) {
	// Artikel löschen
	
	// Prüfen ob Artikel noch Verwendet wird
	$error = false;
	$mysql->select('*', $prefix.'page', "WHERE data like '%".$_GET['id']."%'");
	while ($page = $mysql->fetchRow()) {
		$data = unserialize($page['data']);
		foreach ($data as $data2) {
			if(in_array($_GET['id'], $data2)) {
				$error = true;
				break;
			}
		}
		if($error == true) {
			break;
		}
	}
	
	if($error == true) {
		$style->box('Fehler', n_icon().'Der Artikel wird zu Zeit noch verwendet und kann darum nicht gelöscht werden!'.back_overview());
	} else {
		$mysql->delete($prefix.'article', sql($_GET['id']));
		$style->box('Erfolgreich!', p_icon().'Der Artikel konnte erfolgreich gelöscht werden!'.back_overview());
	}
	
} elseif(view()) {
	// Artikelübersicht
	$list = new liste('Artikelübersicht');
	$columns = array();
	$columns['ID'] = 30;
	$columns['Titel'] = '';
	$columns['Typ'] = '';
	$columns['Datum'] = '';
	$columns['Optionen'] = '';
	$list->add_columns($columns);
	$mysql->select('*', $prefix.'article', 'ORDER BY id ASC');
	while($article = $mysql->fetchRow()) {
		$data = array();
		$data[] = $article['id'];
		$data[] = $article['title'];
		$data[] = ($article['type'] == 0 ? 'Text' : 'Script');
		$data[] = date("d.m.y", $article['date']);
		$data[] = option_icon('edit', INDEX.'&id='.$article['id']).option_icon('delete', INDEX.'&id='.$article['id']);
		$list->add_row($data);
	}
	$style->add($list->get());
}
?>