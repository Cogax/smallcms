<?php
/**
 * 		Datei: 					admin/mod.template.php
 * 		Erstellungsdatum:		25.07.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Templateverwaltung
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

// Neues Template erstellen link
$link = icon_link('Übersicht', 'table', 'index.php?modul=template');
$link .= icon_link('Neues Template', 'image-add-alt', 'index.php?modul=template&action=add');
$style->box('Optionen', $link, 'left');

if(formular('add')) {
	// Neues Template erstellen - Formular
	$form = new form();
	$form->new_group('Template auswählen');
	$form->input('Name:', 'name');
	$options = array();
	$pfad = opendir(option('rel_path')."templates/");
	while ($datei = readdir($pfad)) {
		if ($datei != '.' && $datei != '..' && !is_dir(option('rel_path').'templates/'.$datei) && strrchr($datei, '.') == '.tpl') {
			$options[$datei] = $datei;
		}
	}
	closedir($pfad);
	$form->select('Template', 'path', $options);
	$form->submit('Template hinzufügen');
	$style->box('Neues Template hinzufügen', $form->get());
	
} elseif (entry('add')) {
	// Neues Template erstellen - Eintrag
	
	// Platzhalter raussuchen
	$ph = array();
	$tpl = file_get_contents('../templates/'.$_POST['path']);
	preg_match_all('!\{'.option('ph_start').'\}(.*)\{'.option('ph_end').'\}!isU', $tpl, $while);
	for($i = 0; $i <=count($while[0])-1; $i++) {
		$ph[] = $while[1][$i];
	}
	
	$insert = array();
	$insert['name'] = sql($_POST['name']);
	$insert['path'] = sql($_POST['path']);
	$insert['platzhalter'] = sql(serialize($ph));
	$insert['type'] = sql('0');
	$insert['date'] = sql(time());
	$mysql->insert($insert, $prefix.'template');
	$style->box(p_icon().'Erfolgreich!', 'Das Template wurde erfolgreich erstellt.'.back_overview());
		
} elseif (formular('edit')) {
	// Template bearbeiten - Formular
	$mysql->id_select('*', $prefix.'template', sql($_GET['id']));
	$template = $mysql->fetchRow();
	
	$form = new form();
	$form->new_group('Template auswählen');
	$form->input('Name:', 'name', $template['name']);
	$options = array();
	$pfad = opendir(option('rel_path')."templates/");
	while ($datei = readdir($pfad)) {
		if ($datei != '.' && $datei != '..' && !is_dir(option('rel_path').'templates/'.$datei)) {
			$options[$datei] = $datei;
			if($template['path'] == $datei) {
				$selected = $datei;
			}
		}
	}
	closedir($pfad);
	$form->select('Template', 'path', $options, 1, false, $selected);
	$form->submit('Template bearbeiten');
	$style->box('Änderungen speichern', $form->get());
		
} elseif (entry('edit')) {
	// Template bearbeiten - Eintrag
	
	// Platzhalter raussuchen
	$ph = array();
	$tpl = file_get_contents('../templates/'.$_POST['path']);
	preg_match_all('!\{'.option('ph_start').'\}(.*)\{'.option('ph_end').'\}!isU', $tpl, $while);
	for($i = 0; $i <=count($while[0])-1; $i++) {
		$ph[] = $while[1][$i];
	}
	
	$update = array();
	$update['name'] = sql($_POST['name']);
	$update['path'] = sql($_POST['path']);
	$update['platzhalter'] = sql(serialize($ph));
	$update['type'] = sql('0');
	$update['date'] = sql(time());
	$mysql->update($update, $prefix.'template', sql($_GET['id']));
	$style->box(p_icon().'Erfolgreich!', 'Das Template wurde erfolgreich bearbeitet.'.back_overview());
	
} elseif (delete()) {
	// Template löschen
	
	// prüfen ob Template noch verwendet wird
	$mysql->select('*', $prefix.'page', 'WHERE template = '.sql($_GET['id']));
	if($mysql->count() > 0) {
		$style->box(n_icon().'Fehler!', 'Das Template wird zur Zeit noch verwendet.'.back_overview());
	} else {
		$mysql->delete($prefix.'template', sql($_GET['id']));
		$style->box(p_icon().'Erfolgreich!', 'Das Template konnte wurde erfolgreich gelöscht!'.back_overview());
	}
	
} elseif(view()) {
	// Templatelübersicht
	$list = new liste('Templateübersicht');
	$columns = array();
	$columns['ID'] = 30;
	$columns['Name'] = '';
	$columns['Datum'] = '';
	$columns['Optionen'] = '';
	$list->add_columns($columns);
	
	$mysql->select('*', $prefix.'template', 'ORDER BY ID ASC');
	while($template = $mysql->fetchRow()) {
		$data = array();
		$data[] = $template['id'];
		$data[] = $template['name'];
		$data[] = date("d.m.y", $template['date']);
		$data[] = option_icon('edit', INDEX.'&id='.$template['id']).option_icon('delete', INDEX.'&id='.$template['id']);
		$list->add_row($data);
	}
	$style->add($list->get());
}
?>