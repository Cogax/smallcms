<?php
/**
 * 		Datei: 					admin/mod.product.php
 * 		Erstellungsdatum:		26.12.2010
 * 		Letzte bearbeitung:		21.10.2011
 * 		Beschreibung:			Produkteverwaltung
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

class product
{
	private $mode = array();
	private $request = array();
	
	function __construct($request = false) {
		// Modes setzen
		$this->mode['add_form']				= setMode(array('action' => 'add'), array('send' => false));
		$this->mode['add_cat_form']			= setMode(array('action' => 'add_cat'), array('send' => false));
		$this->mode['add']					= setMode(array('action' => 'add'), array('send' => true));
		$this->mode['add_cat']				= setMode(array('action' => 'add_cat'), array('send' => true));
		$this->mode['edit_form']			= setMode(array('action' => 'edit'), array('send' => false));
		$this->mode['edit_cat_form']		= setMode(array('action' => 'edit_cat'), array('send' => false));
		$this->mode['edit']					= setMode(array('action' => 'edit'), array('send' => true));
		$this->mode['edit_cat']				= setMode(array('action' => 'edit_cat'), array('send' => true));
		$this->mode['show']					= setMode(array('action' => 'show'), array('send' => false));
		$this->mode['delete']				= setMode(array('action' => 'delete'), array('send' => false));
		$this->mode['delete_cat']			= setMode(array('action' => 'delete_cat'), array('send' => false));
		$this->mode['overview']				= setMode(array(), array());
		
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
			case $this->mode['add_form']['check']:				$this->add_form(); break;
			case $this->mode['add_cat_form']['check']:			$this->add_cat_form(); break;
			case $this->mode['add']['check']:					$this->add(); break;
			case $this->mode['add_cat']['check']:				$this->add_cat(); break;
			case $this->mode['edit_form']['check']:				$this->edit_form(); break;
			case $this->mode['edit_cat_form']['check']:			$this->edit_cat_form(); break;
			case $this->mode['edit']['check']:					$this->edit(); break;
			case $this->mode['edit_cat']['check']:				$this->edit_cat; break;
			case $this->mode['show']['check']:					$this->show(); break;
			default:											$this->overview(); break;
		}
	}
	
	/**
	 * Optionenbox links
	 */
	function option_box() {
		global $style;
		
		$link = icon_link('Übersicht', 'table', $this->mode['overview']['link']);
		$link .= icon_link('Neues Produkt', 'input-add', $this->mode['add_form']['link']);
		$link .= icon_link('Neue Kategorie', 'table-add', $this->mode['add_cat_form']['link']);
		$style->box('Optionen', $link, 'left');
	}
	
	/**
	 * Baumstruktur links
	 */
	function tree_box() {
		global $style, $mysql, $prefix;
		
		$tree = new tree();
		$tree->show_links();
		$base = $tree->add('Produktkategorien', -1, ''); // Base
		$res_cat = $mysql->select('id,name', $prefix.'product_cat', 'ORDER by id ASC');
		while ($cat = $mysql->fetchRow($res_cat)) {
			$parent = $tree->add($cat['name'], $base);
			$res_prod = $mysql->select('id,name', $prefix.'product', 'WHERE cat = '.sql($cat['id']));
			if($mysql->count() > 0) {
				while ($prod = $mysql->fetchRow($res_prod)) {
					$tree->add($prod['name'], $parent, INDEX.'&action=show&id='.$prod['id']);
				}
			}
		}
		$style->box('Produktübersicht', $tree->get(), 'left');
	}
	
	/**
	 * Produkt hinzufügen formular
	 */
	function add_form() {
		global $style, $mysql, $prefix;
		
		// Prüfen ob min. eine Kategorie besteht
		$mysql->select('id,name', $prefix.'product_cat');
		if($mysql->count() == 0) {
			$style->box(n_icon().'Fehler!', 'Es existiert keine Produktkategorie! Bitte erstelle zu erst eine Produktkategorie, in welche das Produkt eingefügt werden soll.'.back_overview('modul_cat'));
		} else {
			$form = new form();	
			$form->new_group('Produktinformationen');
			$options = array();
			while ($cat = $mysql->fetchRow()) {
				$options[$cat['id']] = $cat['name'];
			}
			$form->select('Kategorie:', 'cat', $options);
			$form->input('Name:', 'name');
			$form->input('Preis:', 'price', '', 'text', 20);
			$form->textarea('Beschreibung:', 'description');
			$options = array();
			$options['0'] = 'Deaktiviert';
			$options['1'] = 'Aktiviert';
			$form->select('Status', 'status', $options);
			$form->submit();
			$style->box('Neues produkt erstellen', $form->get());
		}
	}
	
	/**
	 * Produktkategorie hinzufügen Formular
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
	 * Produkt hinzufügen eintrag
	 */
	function add(){
		global $style, $mysql, $prefix;
		
		$insert = array();
		$insert['name'] = sql($this->request['name']);
		$insert['cat'] = sql($this->request['cat']);
		$insert['price'] = sql($this->request['price']);
		$insert['description'] = sql($this->request['description']);
		$insert['status'] = sql($this->request['status']);
		$insert['date'] = sql(time());
		$mysql->insert($insert, $prefix.'product');
		$style->box(p_icon().'Erfolgreich!', 'Das Produkt wurde erfolgreich erstellt.'.back_overview());
	}
	
	/**
	 * Produkt kategorie erstellen eintrag
	 */
	function add_cat() {
		global $style, $mysql, $prefix;
		
		$insert = array();
		$insert['name'] = sql($this->request['name']);
		$mysql->insert($insert, $prefix.'product_cat');
		$style->box(p_icon().'Erfolgreich!', 'Die Kategorie wurde erfolgreich erstellt.'.back_overview());
	}
	
	/**
	 * Produkt Bearbeiten Formular
	 */
	function edit_form() {
		global $style, $mysql, $prefix;
		
		// Produkt bearbeiten - Formular
		$mysql->id_select('id,name,price,cat,description,status', $prefix.'product', sql($this->request['id']));
		$product = $mysql->fetchRow();
		$form = new form();
		$form->new_group('Produktinformationen');
		$options = array();
		$mysql->select('id,name', $prefix.'product_cat');
		while ($cat = $mysql->fetchRow()) {
			$options[$cat['id']] = $cat['name'];
		}
		$form->select('Kategorie:', 'cat', $options, 1, 0, $product['cat']);
		$form->input('Name:', 'name', $product['name']);
		$form->input('Preis:', 'price', $product['price'], 'text', 20);
		$form->textarea('Beschreibung:', 'description', $product['description']);
		$options = array();
		$options['0'] = 'Deaktiviert';
		$options['1'] = 'Aktiviert';
		$form->select('Status', 'status', $options, 1, 0, $product['status']);
		$form->submit();
		$style->box('Produkt bearbeiten', $form->get());
	}
	
	/**
	 * Produkt Kategorie bearbeiten Formular
	 */
	function edit_cat_form() {
		global $style, $mysql, $prefix;
		
		$mysql->id_select('id,name', $prefix.'product_cat', sql($this->request['id']));
		$cat = $mysql->fetchRow();
		$form = new form();
		$form->new_group('Kategorie Informationen');
		$form->input('Name:', 'name', $cat['name']);
		$form->submit();
		$style->box('Kategorie bearbeiten', $form->get());
	}
	
	/**
	 * Produkt bearbeiten Eintrag
	 */
	function edit() {
		global $style, $mysql, $prefix;
		
		$update = array();
		$update['name'] = sql($this->request['name']);
		$update['cat'] = sql($this->request['cat']);
		$update['price'] = sql($this->request['price']);
		$update['description'] = sql($this->request['description']);
		$update['status'] = sql($this->request['status']);
		$update['date'] = sql(time());
		$mysql->update($update, $prefix.'product', sql($this->request['id']));
		$style->box(p_icon().'Bearbeitung erfolgreich!', 'Das Produkt konnte erfolgreich bearbeitet werden!'.back_overview());
	}
	
	/**
	 * Produkt Kategorie bearbeiten Eintrag
	 */
	function edit_cat() {
		global $style, $mysql, $prefix;
		
		$update = array();
		$update['name'] = sql($this->request['name']);
		$mysql->update($update, $prefix.'product_cat', sql($this->request['id']));
		$style->box(p_icon().'Bearbeitung erfolgreich!', 'Die Kategorie konnte erfolgreich bearbeitet werden!'.back_overview());
	}
	
	/**
	 * Produkt anzeigen
	 */
	function show() {
		global $style, $mysql, $prefix;
		
		$mysql->id_select('id,name,price,cat,description,status', $prefix.'product', sql($this->request['id']));
		$product = $mysql->fetchRow();
		$list = new liste('Produkt: '.$product['name']);
		$list->add_columns(array(' ' => '50%', '' => ''));
		$list->add_row(array(liste_title('ID'), $product['id']));
		$list->add_row(array(liste_title('Kategorie'), $mysql->get_from_id('name', $prefix.'product_cat', $product['cat'])));
		$list->add_row(array(liste_title('Name'), $product['name']));
		$list->add_row(array(liste_title('Preis'), $product['price']));
		$list->add_row(array(liste_title('Status:'), ($product['status'] == '0' ? 'Deaktiviert' : 'Aktiviert')));
		$list->add_row(array(liste_title('ID'), $product['id']));
		$style->add($list->get());
		// Bearbeiten Button
		$form = new form();
		$form->button('Datensatz bearbeiten', $this->mode['edit_form']['link'].'&id='.$this->request['id']);
		$style->add($form->get());
	}
	
	/**
	 * Produkt löschen
	 */
	function delete() {
		global $style, $mysql, $prefix;
		
		$mysql->delete($prefix.'product', sql($this->request['id']));
		$style->box(p_icon().'Erfolgreich!', 'Das Produkt wurde erfolgreich gelöscht!'.back_overview());
	}
	
	/**
	 * Produkt Kategorie löschen
	 */
	function delete_cat() {
		global $style, $mysql, $prefix;
		
		// Prüfen ob noch min. ein Produkt in der Kategorie liegt
		$mysql->select('id', $prefix.'product', 'WHERE cat = '.sql($this->request['id']));
		if ($mysql->count() > 0) {
			$style->box(n_icon().'Fehler!', 'Produktekategorie konnte nicht gelöscht werden! Vergewissere dich, dass keine
			Produkte mehr in dieser Kategorie sind.'.back_overview());
		} else {
			$mysql->delete($prefix.'product_cat', sql($this->request['id']));
			$style->box(p_icon().'Erfolgreich!', 'Die Kategorie wurde erfolgreich gelöscht!'.back_overview());
		}
	}
	
	/**
	 * Produkt Übersicht
	 */
	function overview() {
		global $style, $mysql, $prefix;
		
		// Kategorieübersicht mit Optionen
		$res_cat = $mysql->select('*', $prefix.'product_cat', 'ORDER BY id ASC');
		while ($cat = $mysql->fetchRow($res_cat)) {
			$list = new liste('Produktkategorie: '.$cat['name']);
			$columns = array();
			$columns['ID'] = 30;
			$columns['Name'] = '';
			$columns['Wert'] = '';
			$columns['Beschreibung'] = '';
			$columns['Optionen'] = '';
			$list->add_columns($columns);
			
			$res_opt = $mysql->select('id,name,price,description', $prefix.'product', 'WHERE cat = '.sql($cat['id']));
			while ($product = $mysql->fetchRow($res_opt)) {
				$data = array();
				$data[] = $product['id'];
				$data[] = $product['name'];
				$data[] = $product['price'].option('currency');
				$data[] = short($product['description']);
				$data[] = 	option_icon('edit', $this->mode['edit_form']['link'].'&id='.$cat['id']).
							option_icon('delete', $this->mode['delete']['link'].'&id='.$cat['id']).
							option_icon('show', $this->mode['show']['link'].'&id='.$cat['id']);
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
		$mysql->select('id,name', $prefix.'product_cat', 'ORDER BY id ASC');
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

$prdc = new product();
$prdc->main();
?>