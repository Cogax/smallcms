<?php
/**
 * 		Datei: 					admin/mod.option.php
 * 		Erstellungsdatum:		25.07.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Verwaltung der Einstellungen
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

class option
{
	private $mode = array();
	private $request = array();
	
	function __construct($request = false) {
		// Modes setzen
		$this->mode['add_form'] 		= setMode(array('action' => 'add'), array('send' => false));
		$this->mode['add'] 				= setMode(array('action' => 'add'), array('send' => true));
		$this->mode['add_cat_form'] 	= setMode(array('action' => 'add_cat'), array('send' => false));
		$this->mode['add_cat'] 			= setMode(array('action' => 'add_cat'), array('send' => true));
		$this->mode['edit_cat_form'] 	= setMode(array('action' => 'edit_cat'), array('send' => false));
		$this->mode['edit_cat'] 		= setMode(array('action' => 'edit_cat'), array('send' => true));
		$this->mode['edit_form'] 		= setMode(array('action' => 'edit'), array('send' => false));
		$this->mode['edit'] 			= setMode(array('action' => 'edit'), array('send' => true));
		$this->mode['delete'] 			= setMode(array('action' => 'delete'), array('send' => true));
		$this->mode['delete_cat'] 		= setMode(array('action' => 'delete_cat'), array('send' => true));
		$this->mode['show'] 			= setMode(array('action' => 'show'), array('send' => false));
		$this->mode['overview'] 		= setMode(array(), array());
		
		// Request Handling
		if($request) {
			$this->request = $request;
		} else {
			$this->request = array_merge($_POST, $_GET);
		}
	}
	
	
	/**
	 * Hauptmethode
	 */
	function main() {
		// Immer anzeigen
		$this->option_box();
		$this->tree_box();
		
		switch (true) {
			case $this->mode['add_form']['check']:			$this->add_form(); break;
			case $this->mode['add']['check']:				$this->add(); break;
			case $this->mode['add_cat_form']['check']:		$this->add_cat_form(); break;
			case $this->mode['add_cat']['check']: 			$this->add_cat(); break;
			case $this->mode['edit_form']['check']:			$this->edit_form(); break;
			case $this->mode['edit']['check']:				$this->edit(); break;
			case $this->mode['edit_cat_form']['check']: 	$this->edit_cat_form(); break;
			case $this->mode['add_cat']['check']:			$this->add_cat(); break;
			case $this->mode['delete']['check']:			$this->delete(); break;
			case $this->mode['delete_cat']['check']:		$this->delete_cat(); break;
			case $this->mode['show']['check']:				$this->show(); break;
			default:	 									$this->overview(); break;
		}
	}
	
	/**
	 * Optionenbox
	 */
	function option_box() {
		global $style;
		// ---- Optionenliste
		$link = icon_link('Übersicht', 'table', $this->mode['overview']['link']);
		$link .= icon_link('Option erstellen', 'input-add', $this->mode['add_form']['link']);
		$link .= icon_link('Kategorie erstellen', 'input-add', $this->mode['add_cat_form']['link']);
		$style->box('Optionen', $link, 'left');
	}
	
	/**
	 * Baumstrukturbox
	 */
	function tree_box() {
		global $style, $mysql, $prefix;
		// ----- Optionen Übersicht
		$tree = new tree();
		$tree->show_links();
		$base = $tree->add('Optionkategorien', -1, ''); // Base
		$res_cat = $mysql->select('*', $prefix.'option_cat', 'ORDER by id ASC');
		while ($cat = $mysql->fetchRow($res_cat)) {
			$parent = $tree->add($cat['name'], $base);
			$res_option = $mysql->select('id,name', $prefix.'option', 'WHERE cat = '.sql($cat['id']));
			if($mysql->count() > 0) {
				while ($option = $mysql->fetchRow($res_option)) {
					$tree->add($option['name'], $parent, $this->mode['show']['link'].'&id='.$option['id']);
				}
			}
		}
		$style->box('Optionenübersicht', $tree->get(), 'left');
	}
	
	/**
	 * Option hinzufügen Formular
	 */
	function add_form() {
		global $mysql, $prefix, $style;
	
		$form = new form();
		$form->new_group('Neue Option');
		$opts = array();
		$mysql->select('id,name', $prefix.'option_cat');
		while ($cat = $mysql->fetchRow()) {
			$opts[$cat['id']] = $cat['name'];
		}
		$form->select('Kategorie:', 'cat', $opts);
		$form->input('Name:', 'name');
		$form->input('Wert:', 'value');
		$form->textarea('Beschreibung:', 'description');
		$form->submit('Option erstellen');
		$style->box('Neue Option erstellen', $form->get());
	}
	
	/**
	 * Option hinzufügen Eintrag
	 */
	function add() {
		global $mysql, $prefix, $style;
		
		$insert = array();
		$insert['name'] = sql($this->request['name']);
		$insert['cat'] = sql($this->request['cat']);
		$insert['value'] = sql($this->request['value']);
		$insert['description'] = sql($this->request['description']);
		$mysql->insert($insert, $prefix.'option');
		$style->box(p_icon().'Erfolgreich!', 'Die Option wurde erfolgreich erstellt.'.back_overview());
	}
	
	/**
	 * Kategorie hinzufügen Formular
	 */
	function add_cat_form() {
		global $style;
		
		$form = new form();
		$form->new_group('Kategorie Informationen');
		$form->input('Name:', 'name');
		$form->submit();
		$style->box('Kategorie erstellen', $form->get());
	}
	
	/**
	 * Kategorie hinzufügen Eintrag
	 */
	function add_cat() {
		global $mysql, $prefix, $style;
		
		$insert = array();
		$insert['name'] = sql($this->request['name']);
		$mysql->insert($insert, $prefix.'option_cat');
		$style->box(p_icon().'Erfolgreich!', 'Die Kategorie wurde erfolgreich erstellt.'.back_overview());
	}	
	
	/**
	 * Option bearbeiten Formular
	 */
	function edit_form() {
		global $mysql, $prefix, $style;
		
		$mysql->id_select('*', $prefix.'option', sql($this->request['id']));
		$option = $mysql->fetchRow();
		$form = new form();
		$form->new_group('Option bearbeiten');
		$opts = array();
		$mysql->select('id,name', $prefix.'option_cat');
		while ($cat = $mysql->fetchRow()) {
			$opts[$cat['id']] = $cat['name'];
		}
		$form->select('Kategorie:', 'cat', $opts, 1, false, $option['cat']);
		$form->input('Name:', 'name', $option['name']);
		$form->input('Wert:', 'value', $option['value']);
		$form->textarea('Beschreibung:', 'description', $option['description']);
		$form->submit('Änderungen speichern');
		$style->box('Option bearbeiten', $form->get());
	}
	
	/**
	 * Option bearbeiten Eintrag
	 */
	function edit() {
		global $mysql, $prefix, $style;
		
		$update = array();
		$update['name'] = sql($this->request['name']);
		$update['cat'] = sql($this->request['cat']);
		$update['value'] = sql($this->request['value']);
		$update['description'] = sql($this->request['description']);
		$mysql->update($update, $prefix.'option', sql($this->request['id']));
		$style->box(p_icon().'Erfolgreich!', 'Die Option wurde erfolreich bearbeitet.'.back_overview());
	}
	
	/**
	 * Kategorie bearbeiten Formular
	 */
	function edit_cat_form() {
		global $mysql, $prefix, $style;
		
		$mysql->id_select('*', $prefix.'option_cat', sql($this->request['id']));
		$cat = $mysql->fetchRow();
		$form = new form();
		$form->new_group('Kategorie Informationen');
		$form->input('Name:', 'name', $cat['name']);
		$form->submit();
		$style->box('Kategorie bearbeiten', $form->get());
	}
	
	/**
	 * Kategorie bearbeiten Eintrag
	 */
	function edit_cat() {
		global $mysql, $prefix, $style;
		
		$update = array();
		$update['name'] = sql($this->request['name']);
		$mysql->update($update, $prefix.'option_cat', sql($this->request['id']));
		$style->box(p_icon().'Bearbeitung erfolgreich!', 'Die Kategorie konnte erfolgreich bearbeitet werden!'.back_overview());
	}
	
	/**
	 * Option anzeigen
	 */
	function show() {
		global $mysql, $prefix, $style;
		
		$mysql->id_select('*', $prefix.'option', sql($this->request['id']));
		$option = $mysql->fetchRow();
		$list = new liste('Option: '.$option['name']);
		$list->add_columns(array('' => '50%', ' ' => ''));
		$list->add_row(array(liste_title('ID'), $option['id']));
		$list->add_row(array(liste_title('Name'), $option['name']));
		$list->add_row(array(liste_title('Kategorie'), $mysql->get_from_id('name', $prefix.'option_cat', sql($option['cat']))));
		$list->add_row(array(liste_title('Wert'), $option['value']));
		$list->add_row(array(liste_title('Beschreibung'), $option['description']));
		$style->add($list->get());
		
		// Bearbeiten Button
		$form = new form();
		$form->button('Datensatz bearbeiten', $this->mode['edit']['link'].'&id='.$this->request['id']);
		$style->add($form->get());
	}
	
	/**
	 * Option löschen
	 */
	function delete() {
		global $mysql, $prefix, $style;
		
		$mysql->delete($prefix.'option', sql($this->request['id']));
		$style->box(p_icon().'Erfolgreich!', 'Die Option wurde erfolgreich gelöscht!'.back_overview());
	}
	
	/**
	 * Kategorie löschen
	 */
	function delete_cat() {
		global $mysql, $prefix, $style;
		
		// Prüfen ob noch eine Option in der Kategorie liegt
		$mysql->select('*', $prefix.'option', 'WHERE cat = '.sql($this->request['id']));
		if ($mysql->count() > 0) {
			$style->box(n_icon().'Fehler!', 'Optionkategorie konnte nicht gelöscht werden! Vergewissere dich, dass keine
			Optionen mehr in dieser Kategorie sind.'.back_overview());
		} else {
			$mysql->delete($prefix.'option_cat', sql($this->request['id']));
			$style->box(p_icon().'Erfolgreich!', 'Die Kategorie wurde erfolgreich gelöscht!'.back_overview());
		}
	}
	
	/**
	 * Übersicht
	 */
	function overview() {
		global $mysql, $prefix, $style;
		
		$res_cat = $mysql->select('*', $prefix.'option_cat', 'ORDER BY id ASC');
		while ($cat = $mysql->fetchRow($res_cat)) {
			$list = new liste('Optionkategorie: '.$cat['name']);
			$columns = array();
			$columns['ID'] = 30;
			$columns['Name'] = '';
			$columns['Wert'] = '';
			$columns['Beschreibung'] = '';
			$columns['Optionen'] = '';
			$list->add_columns($columns);
			
			$res_opt = $mysql->select('id,name,value,description', $prefix.'option', 'WHERE cat = '.sql($cat['id']));
			while ($option = $mysql->fetchRow($res_opt)) {
				$data = array();
				$data[] = $option['id'];
				$data[] = $option['name'];
				$data[] = short($option['value']);
				$data[] = short($option['description']);
				$data[] = 	option_icon('edit', $this->mode['edit_form']['link'].'&id='.$option['id']).
							option_icon('delete', $this->mode['delete']['link'].'&id='.$option['id']).
							option_icon('show', $this->mode['show']['link'].'&id='.$option['id']);
				$list->add_row($data);
			}
			$style->add($list->get());
			$style->add('<br /><br />');
		}
		
		// Kategorieübersicht zum bearbeiten der Kategorien
		$liste = new liste('Kategorieübsersicht');
		$columns = array();
		$columns['ID'] = 30;
		$columns['Name'] = '';
		$columns['Optionen'] = '';
		$liste->add_columns($columns);
		$mysql->select('*', $prefix.'option_cat', 'ORDER BY id ASC');
		while ($cat = $mysql->fetchRow()) {
			$data = array();
			$data[] = $cat['id'];
			$data[] = $cat['name'];
			$data[] = 	option_icon('edit', $this->mode['edit_cat_form']['link'].'&id='.$cat['id']).
						option_icon('delete', $this->mode['delete_cat']['link'].'&id='.$cat['id']);
			$liste->add_row($data);
		}
		$style->add($liste->get());	
	}
}

$opt = new option();
$opt->main();
?>