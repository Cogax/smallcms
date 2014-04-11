<?php
/**
 * 		Datei: 					admin/func.functions.php
 * 		Erstellungsdatum:		25.07.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Verschiedene Funktionen für das Backend
 * 		Autor:					Andreas Gyr
 */

## Mysql Injection Schutz:
function sql($value, $operator = 'no')
{
	// Überflüssige Maskierungen entfernen
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}

	// In Anführungszeichen setzen, falls keine Zahl
	// oder ein numerischer String vorliegt:
	if (!is_numeric($value)) {
		if ($operator == 'no') {
			$value = "'" . mysql_real_escape_string($value) . "'";
		} else {
			$value = mysql_real_escape_string($value);
		}
	}

	// Falls leer, einen leeren String simulieren:
	if ($value == "") {
		$value = "''";
	}

	// Rückgabe der Variabel:
	return $value;
}

## IF für Formular
function formular($mode, $step = '') {
	global $_POST, $_GET;
	if($mode != 'edit' && $mode != 'add')
		return false;
	
	return (
		isset($_GET['action'])&& 
		$_GET['action'] == $mode &&
		(empty($step) ? (!isset($_POST['send'])) : (isset($_GET['step']) ? $_GET['step'] == $step : 0))
	);	
}

## IF für Eintrag
function entry($mode) {
	global $_POST, $_GET;
	if($mode != 'edit' && $mode != 'add')
		return false;
	
	return (
		isset($_GET['action']) && 
		$_GET['action'] == $mode &&
		isset($_POST['send'])
	);	
}

## IF für Löschen
function delete() {
	global $_POST, $_GET;

	return (
		isset($_GET['action']) && 
		$_GET['action'] == 'delete' &&
		isset($_GET['id'])
	);	
}

## IF für Anzeigen
function show() {
	global $_POST, $_GET;

	return (
		isset($_GET['action']) && 
		$_GET['action'] == 'show' &&
		isset($_GET['id'])
	);	
}

## IF für Anzeigen
function view() {
	return (
		true
	);	
}

## Simpler Link erstellen
function navigation_link($href, $name) {
	return '<a href="index.php?modul='.$href.'">'.$name.'</a> ';
}

## Optionen laden
function option($option_name) {
	global $prefix, $mysql_func;
	$sql = "SELECT * FROM ".$prefix."option WHERE name = ".sql($option_name).";";
	$mysql_func->query($sql);
	$option = $mysql_func->fetchRow();
	return $option['value'];
}

## Positiv Icon
// erfolgreich
function p_icon() {
	return '<img src="../images/admin/icons/system-tick.png" width="20" height="20" /> ';
}

## Negativ Icon
// fehler
function n_icon() {
	return '<img src="../images/admin/icons/system-error.png" width="20" height="20" /> ';
}

## Warnung Icon
// warnung
function w_icon() {
	return '<img src="../images/admin/icons/system-info.png" width="20" height="20" /> ';
}

## Zurück zur übersicht link
function back_overview($modul = '') {
	if($modul == '') {
		$x = $_SERVER['REQUEST_URI'];
		$x = str_replace(strstr($x, '&'), '', substr(strrchr($x, '/'), 1));
	} else {
		$x = 'index.php?modul='.$modul;
	}
	return '<br /><strong><a style="color:#15428B;" href="'.$x.'">Zurück zur Übersicht</a></strong>';
}

function back_mode($mode) {
	return '<br /><strong><a style="color:#15428B;" href="'.$mode['link'].'">Zurück</a></strong>';
}

## Zurück zur Index link
function back_index() {
	return '<br /><strong><a style="color:#15428B;" href="index.php">Zurück</a></strong>';
}

function template_name($tpl_id) {
	global $prefix, $mysql_func;
	$sqly = "SELECT * FROM ".$prefix."template WHERE id = ".sql($tpl_id).";";
	$mysql_func->query($sqly);
	$tpl = $mysql_func->fetchRow();
	return $tpl['name'];
}

function article_title($art_id) {
	global $prefix, $mysql_func;
	$sql = "SELECT * FROM ".$prefix."article WHERE id = ".sql($art_id).";";
	$mysql_func->query($sql);
	$art = $mysql_func->fetchRow();
	return $art['title'];
}

function liste_title($title) {
	return '<strong style="color:#15428B;">'.$title.'</strong>';
}

function edit_link($id, $modul = '') {
	if($modul == '') {
		$x = $_SERVER['REQUEST_URI'];
		$x = str_replace(strstr($x, '&'), '', substr(strrchr($x, '/'), 1));
	} else {
		$x = 'index.php?modul='.$modul;
	}
	return '<br /><strong><a style="color:#15428B;" href="'.$x.'&action=edit&id='.$id.'">Bearbeiten</a></strong>';
}

function option_icon($option = 'edit', $href_without_action) {
	$x = 0;
	if($href_without_action!=str_replace("action=","",$href_without_action)) {
		$x = 1;
	}
	switch ($option) {
		case 'edit':
			return
			'<a href="'.($x ? $href_without_action : $href_without_action.'&action=edit').'"><img src="../images/admin/icons/edit.png" width="20" height="20" alt="Bearbeiten" title="Bearbeiten" /></a>';
		case 'delete':
			return
			'<a href="'.($x ? $href_without_action : $href_without_action.'&action=delete').'" onclick="return delete_link();"><img src="../images/admin/icons/system-delete.png" width="20" height="20" alt="Löschen" title="Löschen" /></a>';
		case 'show':
			return
			'<a href="'.($x ? $href_without_action : $href_without_action.'&action=show').'"><img src="../images/admin/icons/file-go.png" width="20" height="20" alt="Anzeigen" title="Anzeigen" /></a>';
	}
}

function icon_link($beschriftung, $icon, $href) {
	return
	'<img src="../images/admin/icons/'.$icon.'.png" height="20" width="20" alt="'.$beschriftung.'" title="'.$beschriftung.'" /> 
	<a href="'.$href.'">'.htmlentities($beschriftung).'</a><br />';
}

function link_icon($beschriftung, $icon, $code) {
	return
	'<a '.$code.'><img src="../images/admin/icons/'.$icon.'.png" height="20" width="20" alt="'.$beschriftung.'" title="'.htmlentities($beschriftung).'" /></a>';
}

function short($str, $length = 30) {
	return (strlen($str) > $length ? substr($str, 0, $length).'..': $str);
}

function setMode($get_arr, $post_arr = false) {
	$return = array();
	$return['link'] = INDEX;
	$return['check'] = true;
	
	// Get
	foreach ($get_arr as $key => $value) {
		$return['link'] .= '&'.$key.'='.$value;
		if (!isset($_GET[$key]) || $_GET[$key] != $value) {
			$return['check'] = false;
		}
	}
	
	// Post
	if (is_array($post_arr)) {
		foreach ($post_arr as $key => $value) {
			if ($key == 'send') {
				if (@isset($_POST['send']) && $value == false) {
					$return['check'] = false;
				}
			} elseif (!@isset($_POST[$key]) || (@$_POST[$key] != $value)) {
				$return['check'] = false;
			}
		}
	}
	
	return $return;
}
?>