<?php
/**
 * 		Datei: 					admin/mod.bill.php
 * 		Erstellungsdatum:		25.12.2010
 * 		Letzte bearbeitung:		16.10.2011
 * 		Beschreibung:			Rechnungsmodul
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

// Ajax laden
$ajax = new Ajax();
require_once('ajax/ajax.bill.php');

class bill
{
	private $mode = array();
	private $request = array();
	
	public $status = array(
		1 => '<span style="color:orange;">Offen</span>',
		2 => '<span style="color:green;">Bezahlt</span>',
		3 => '<span style="color:red;">Mahnung 1</span>',
		4 => '<span style="color:red;">Mahnung 2</span>',
		5 => '<span style="color:red;">Mahnung 3</span>',
		6 => '<span style="color:red;">Betreibung</span>'
	);
	
	function __construct($request = false){
		$this->mode['add_form'] 		= setMode(array('action' => 'add'), array('send' => false));
		$this->mode['add_tpl_form'] 	= setMode(array('action' => 'add_tpl'), array('send' => false));
		$this->mode['add'] 				= setMode(array('action' => 'add'), array('send' => true));
		$this->mode['add_tpl'] 			= setMode(array('action' => 'add_tpl'), array('send' => true));
		$this->mode['edit_form'] 		= setMode(array('action' => 'edit'), array('send' => false));
		$this->mode['edit_tpl_form'] 	= setMode(array('action' => 'edit_tpl'), array('send' => false));
		$this->mode['edit'] 			= setMode(array('action' => 'edit'), array('send' => true));
		$this->mode['edit_tpl'] 		= setMode(array('action' => 'edit_tpl'), array('send' => true));
		$this->mode['show'] 			= setMode(array('action' => 'show'), array('send' => false));
		$this->mode['show_tpl'] 		= setMode(array('action' => 'show_tpl'), array('send' => false));
		$this->mode['print'] 			= setMode(array('action' => 'print'), array('send' => false));
		$this->mode['delete']			= setMode(array('action' => 'delete'), array('send' => false));
		$this->mode['delete_tpl']		= setMode(array('action' => 'delete_tpl'), array('send' => false));
		$this->mode['paid']				= setMode(array('action' => 'paid'), array('send' => false));
		$this->mode['overview']			= setMode(array(), array());
		
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
		
		switch (true) {
			case $this->mode['add_form']['check']: 			$this->add_form(); break;
			case $this->mode['add_tpl_form']['check']: 		$this->add_tpl_form(); break;
			case $this->mode['add']['check']: 				$this->add(); break;
			case $this->mode['add_tpl']['check']: 			$this->add_tpl(); break;
			case $this->mode['edit_form']['check']: 		$this->edit_form(); break;
			case $this->mode['edit_tpl_form']['check']: 	$this->edit_tpl_form(); break;
			case $this->mode['edit']['check']: 				$this->edit(); break;
			case $this->mode['edit_tpl']['check']: 			$this->edit_tpl(); break;
			case $this->mode['show']['check']: 				$this->show(); break;
			case $this->mode['show_tpl']['check']: 			$this->show_tpl(); break;
			case $this->mode['print']['check']: 			$this->b_print(); break;
			case $this->mode['delete']['check']: 			$this->delete(); break;
			case $this->mode['delete_tpl']['check']: 		$this->delete_tpl(); break;
			case $this->mode['paid']['check']: 				$this->paid(); break;
			default:										$this->overview(); break;
		}
	}
	
	/**
	 * Optionenbox links
	 */
	function option_box() {
		global $style;
		
		// neue Rechnung erstellen link
		$link = icon_link('Übersicht', 'table', $this->mode['overview']['link']);
		$link .= icon_link('Neue Rechnung', 'input-add', $this->mode['add_form']['link']);
		$link .= icon_link('Neues Template', 'image-add-alt', $this->mode['add_tpl_form']['link']);
		$style->box('Optionen', $link, 'left');
	}
	
	/**
	 * Neue Rechnung Formular
	 */
	function add_form() {
		global $style, $mysql, $prefix, $ajax;
		
		// Rechnung erstellen - Formular
		$form = new form();
		$form->new_group('Kundeninformationen');
		$mysql->select('id,vorname,nachname', $prefix.'customer');
		$options = array();
		while ($customer = $mysql->fetchRow()) {
			$options[$customer['id']] = $customer['vorname'].' '.$customer['nachname'];
		}
		$form->select('Kunde:', 'customer', $options);
		$form->checkbox('Andere rechnungsadresse?', 'other', array('Ja' => 'Ja'));
		$form->textarea('Rechnungsadresse:', 'bill_address', '', 5, 60, false);
		
		// Produkte
		$form->new_group('Produkt hinzufügen');
		$options = array();
		$mysql->select('id,name', $prefix.'product', 'ORDER BY id ASC'); // Produkte auslesen
		while ($product = $mysql->fetchRow()) {
			$options[$product['id']] = $product['name']; // produkte zuordnen
		}
		$form->select('Produkt:', 'product', $options); // Ajax Formularfeld
		$form->input('Menge:', 'menge', '', 'text', 15, 5, 0); // Ajax Formularfeld
		$form->button('Produkt hinzufügen', '', 'onclick="addProduct();"');
		
		// Produkteübersicht Ausgabe der Liste (AJAX)
		$form->new_group('Produkteübersicht');
		// Liste für die erste ansicht. (Keine Einträge!)
		$list = new liste('Produkteübersicht');
		$columns = array();
		$columns['Produkt'] = '';
		$columns['Menge'] = 60;
		$columns['Preis pro Stück'] = '';
		$columns['Preis'] = '';
		$list->add_columns($columns);
		$form->add_code(
			// AJAX: Vorladefunktion ausgeben
			$ajax->getPreload().
			
			// AJAX: Resultat ausgeben
			$ajax->getResult($list->get())
		);
		
		// Ajax Ausführen
		$ajax->run();
		
		// AJAX: JS Code in HTML header einfügen
		$style->add_js($ajax->getJS());
		
		$form->new_group('Rechnungsinformationen');
		$form->input('Referenznummer:', 'referenznummer', '', 'text', 40, 250, 0); // optional
		$form->textarea('Zahlungszweck:', 'zahlungszweck', '', 5, 40, 0); // optional
		$form->textarea('Notizen:', 'notizen', '', 5, 55, 0); // optional
		$form->submit();
		$style->box('Rechnung erstellen', $form->get());
	}
	
	/**
	 * Neues Rechnungstemplate Formular
	 */
	function add_tpl_form() {
		global $style;
		
		$form = new form();
		$form->new_group('Template auswählen');
		$form->input('Name:', 'name');
		$options = array();
		$pfad = opendir(option('rel_path')."templates/");
		while ($datei = readdir($pfad)) {
			if ($datei != '.' && $datei != '..' && !is_dir(option('rel_path').'templates/'.$datei) && substr($datei,0,5) == 'bill.' && strrchr($datei, '.') == '.php') {
				$options[$datei] = $datei;
			}
		}
		closedir($pfad);
		$form->select('Template:', 'path', $options);
		$form->submit('Template hinzufügen');
		$style->box('Neues Template hinzufügen', $form->get());
	}
	
	/**
	 * Neue Rechnung Eintrag
	 */
	function add() {
		global $style, $mysql, $prefix;
		
		$products = array();
		$menges = unserialize(base64_decode($this->request['menges']));
		$keys = unserialize(base64_decode($this->request['keys']));
		$i = 0;
		foreach (unserialize(base64_decode($this->request['products'])) as $product_id) {
			$products[$keys[$i]][$product_id] = $menges[$keys[$i]];
			$i++;
		}
		$insert = array();
		$insert['customer'] = sql($this->request['customer']);
		$insert['kundennummer'] = $mysql->get_from_id('kundennummer', $prefix.'customer', $insert['customer']);
		$mysql->select('id', $prefix.'bill', 'WHERE customer = '.sql($insert['customer']));
		$insert['rechnungsnummer'] = $insert['kundennummer'].$mysql->count();
		$insert['product_data'] = sql(serialize($products));
		$insert['date'] = time();
		$insert['referenznummer'] = sql($this->request['referenznummer']);
		$insert['zahlungszweck'] = sql($this->request['zahlungszweck']);
		$insert['notizen'] = sql($this->request['notizen']);
		$insert['status'] = sql(1); // Rechnung offen
		$insert['rechnungsadresse'] = (@$this->request['other'] == 'on' ? sql($this->request['bill_address']) : sql(''));
		$insert['betrag'] = sql($this->request['preis_total']);
		$mysql->insert($insert, $prefix.'bill');
		$style->box(p_icon().'Erfolgreich!', 'Die Rechnung wurde erfolgreich erstellt.'.back_overview());
	}
	
	/**
	 * Neues Rechnungstemplate Eintrag
	 */
	function add_tpl() {
		global $style, $mysql, $prefix;
		
		$insert = array();
		$insert['name'] = sql($this->request['name']);
		$insert['path'] = sql($this->request['path']);
		$insert['date'] = sql(time());
		$mysql->insert($insert, $prefix.'bill_template');
		$style->box(p_icon().'Erfolgreich!', 'Das Template wurde erfolgreich erstellt.'.back_overview());
	}
	
	/**
	 * Rechnung bearbeiten Formular
	 */
	function edit_form() {
		global $style, $mysql, $prefix, $ajax;
		
		$mysql->id_select('*', $prefix.'bill', sql($this->request['id']));
		$bill = $mysql->fetchRow();
		
		$form = new form();
		$form->new_group('Kundeninformationen');
		$mysql->select('id,vorname,nachname', $prefix.'customer');
		$options = array();
		while ($customer = $mysql->fetchRow()) {
			$options[$customer['id']] = $customer['vorname'].' '.$customer['nachname'];
		}
		$form->select('Kunde:', 'customer', $options, 1, 0, $bill['customer']);
		$form->checkbox('Andere rechnungsadresse?', 'other', array('Ja' => 'Ja'), ($bill['rechnungsadresse'] == '' ? '' : 'Ja'));
		$form->textarea('Rechnungsadresse:', 'bill_adresse', $bill['rechnungsadresse'], 5, 60, false);
		
		
		// Produkte
		$form->new_group('Produkt hinzufügen');
		$options = array();
		$mysql->select('id,name', $prefix.'product', 'ORDER BY id ASC'); // Produkte auslesen
		while ($product = $mysql->fetchRow()) {
			$options[$product['id']] = $product['name']; // produkte zuordnen
		}
		$form->select('Produkt:', 'product', $options); // Ajax Formularfeld
		$form->input('Menge:', 'menge', '', 'text', 15, 5, 0); // Ajax Formularfeld
		$form->button('Produkt hinzufügen', '', 'onclick="addProduct();"');
		
		// Produkteübersicht Ausgabe der Liste (AJAX)
		$form->new_group('Produkteübersicht');
		$JSCode = '';
		// Liste für die erste ansicht. (Keine Einträge!)
		$list = new liste('Produkteübersicht');
		$columns = array();
		$columns['Produkt'] = '';
		$columns['Menge'] = 60;
		$columns['Preis pro Stück'] = '';
		$columns['Preis'] = '';
		$columns['Del'] = '';
		$list->add_columns($columns);
		$total_preis = 0;
		$products = unserialize($bill['product_data']);
		$menges = array();
		$keys = array();
		foreach ($products as $key => $arr) {
			foreach ($arr as $product_id => $menge) {
				// JS Arrays für Produkteliste eintrag
				$JSCode .= 	'products['.$key.'] = '.$product_id.';
						menges['.$key.'] = '.$menge.';
						keys['.$key.'] = '.$key.';
						xyz = '.$key.';
				';
				
				$menges[$key] = $menge; 
				$keys[$key] = $key;
				
				$data = array();
				$data[] = $mysql->get_from_id('name', $prefix.'product', sql($product_id));
				$data[] = (int) $menge;
				$data[] = ((int)$mysql->get_from_id('price', $prefix.'product', sql($product_id))).option('currency');
				$data[] = ($data[1] * $data[2]).option('currency'); // menge * preis pro stück
				$data[] = link_icon('Produkt löschen', 'system-delete', 'onclick="delProduct('.$key.');"');
				// Total preis
				$total_preis +=	$data[3]; // Preis dazurechnen
				$list->add_row($data);
			}
		}
		
		// Hidden formularfelder für übergabe
		$hidden = 	'<input type="hidden" name="products" value="'.base64_encode(serialize($products)).'" />
					<input type="hidden" name="menges" value="'.base64_encode(serialize($menges)).'" />
					<input type="hidden" name="keys" value="'.base64_encode(serialize($keys)).'" />
					<input type="hidden" name="preis_total" value="'.$total_preis.'" />';
		
		$JSCode .= 'xyz++;';
		$ajax->registJSCode($JSCode);
		$data = array();
		$data[] = '<strong>Preis Total:</strong>';
		$data[] = '';
		$data[] = '';
		$data[] = '<strong>'.((int) $total_preis).option('currency').'</strong>';
		$data[] = '';
		$list->add_row($data);
		$form->add_code(
			// AJAX: Vorladefunktion ausgeben
			$ajax->getPreload().
			
			// AJAX: Resultat ausgeben
			$ajax->getResult($list->get().$hidden)
		);
		
		// Ajax Ausführen
		$ajax->run();
		
		// AJAX: JS Code in HTML header einfügen
		$style->add_js($ajax->getJS());
		
		// Rechnungsinfos für einzahlungsschein
		$form->new_group('Rechnungsinformationen');
		$form->input('Referenznummer:', 'referenznummer', $bill['referenznummer'], 'text', 40, 250, 0); // optional
		$form->textarea('Zahlungszweck:', 'zahlungszweck', $bill['zahlungszweck'], 5, 40, 0); // optional
		$form->textarea('Notizen:', 'notizen', $bill['notizen'], 5, 55, 0); // optional
		
		// Rechnungsstatus
		$form->new_group('Rechnungsstatus');
		$form->select('Status', 'status', $this->status, 1, 0, $bill['status']);
		$form->submit();
		$style->box('Rechnung bearbeiten', $form->get());
	}
	
	/**
	 * Rechnungstemplate bearbeiten Formular
	 */
	function edit_tpl_form() {
		global $style, $mysql, $prefix;
		
		$mysql->id_select('*', $prefix.'bill_template', sql($this->request['id']));
		$template = $mysql->fetchRow();
		
		$form = new form();
		$form->new_group('Template auswählen');
		$form->input('Name:', 'name', $template['name']);
		$options = array();
		$pfad = opendir(option('rel_path')."templates/");
		while ($datei = readdir($pfad)) {
			if ($datei != '.' && $datei != '..' && !is_dir(option('rel_path').'templates/'.$datei) && substr($datei,0,5) == 'bill.' && strrchr($datei, '.') == '.php') {
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
	}
	
	/**
	 * Rechnung bearbeiten Eintrag
	 */
	function edit() {
		global $style, $mysql, $prefix;
		
		$products = array();
		$menges = unserialize(base64_decode($this->request['menges']));
		$keys = unserialize(base64_decode($this->request['keys']));
		$i = 0;
		foreach (unserialize(base64_decode($this->request['products'])) as $product) {
			foreach ($product as $id => $m) {
				$products[$keys[$i]][$id] = $menges[$keys[$i]];
				$i++;
			}
		}
		$update = array();
		$update['customer'] = sql($this->request['customer']);
		$update['kundennummer'] = $mysql->get_from_id('kundennummer', $prefix.'customer', $update['customer']);
		$mysql->select('id', $prefix.'bill', 'WHERE customer = '.sql($update['customer']));
		$update['rechnungsnummer'] = $update['kundennummer'].$mysql->count();
		$update['product_data'] = sql(serialize($products));
		$update['date'] = time();
		$update['referenznummer'] = sql($this->request['referenznummer']);
		$update['zahlungszweck'] = sql($this->request['zahlungszweck']);
		$update['notizen'] = sql($this->request['notizen']);
		$update['rechnungsadresse'] = (@$this->request['other'] == 'on' ? sql($this->request['bill_address']) : sql(''));
		$update['betrag'] = sql($this->request['preis_total']);
		$update['status'] = sql($this->request['status']);
		$mysql->update($update, $prefix.'bill', sql($this->request['id']));
		$style->box(p_icon().'Bearbeitung erfolgreich!', 'Die Rechnung konnte erfolgreich bearbeitet werden!'.back_overview());
	}
	
	/**
	 * Rechnungstemplate bearbeiten Eintrag
	 */
	function edit_tpl() {
		global $style, $mysql, $prefix;
		
		$update = array();
		$update['name'] = sql($this->request['name']);
		$update['path'] = sql($this->request['path']);
		$update['date'] = sql(time());
		$mysql->update($update, $prefix.'bill_template', sql($this->request['id']));
		$style->box(p_icon().'Erfolgreich!', 'Das Template wurde erfolgreich bearbeitet.'.back_overview());	
	}
	
	/**
	 * Rechnung anzeigen
	 */
	function show() {
		global $style, $mysql, $prefix;
		
		$mysql->id_select('*', $prefix.'bill', sql($this->request['id']));
		$bill = $mysql->fetchRow();
		
		$list = new liste('Rechnung: '.$bill['rechnungsnummer']);
		$list->add_columns(array('' => '50%', ' ' => ''));
		$list->add_row(array(liste_title('ID'), $bill['id']));
		$mysql->id_select('vorname,nachname', $prefix.'customer', sql($bill['customer']));
		$customer = $mysql->fetchRow();
		$list->add_row(array(liste_title('Kunde'), $customer['vorname'].$customer['nachname']));
		$list->add_row(array(liste_title('Kundennummer'), $bill['kundennummer']));
		$list->add_row(array(liste_title('Rechnungsadresse'), $bill['rechnungsadresse']));
		$list->add_row(array(liste_title('Betrag'), ($bill['betrag']).option('currency')));
		$list->add_row(array(liste_title('Status'), $this->status[$bill['status']]));
		//$list->add_row(array(liste_title('Template'), $bill['template']));
		$list->add_row(array(liste_title('Referenznummer'), $bill['referenznummer']));
		$list->add_row(array(liste_title('Zahlungszweck'), $bill['zahlungszweck']));
		$list->add_row(array(liste_title('Notizen'), $bill['notizen']));
		// Produkteliste erstellen
		$product_data = unserialize($bill['product_data']);
		$products = '';
		foreach ($product_data as $key => $arr) {
			foreach ($arr as $product_id => $menge_anz) {
				$products .= $mysql->get_from_id('name', $prefix.'product', sql($product_id)).' ('.$menge_anz.')<br />';
			}
		}
		$list->add_row(array(liste_title('Produkte'), $products));
		$list->add_row(array(liste_title('Erstellungsdatum'), date('d.m.Y H:i', $bill['date'])));
		$style->add($list->get());
		
		// Bearbeiten Button
		$form = new form();
		$form->button('Datensatz bearbeiten', $this->mode['edit_form']['link'].'&id='.$this->request['id']);
		$style->add($form->get());
	}
	
	/**
	 * Rechnungstemplate anzeigen
	 */
	function show_tpl() {
		global $style, $mysql, $prefix;
		
		$mysql->id_select('*', $prefix.'bill_template', sql($this->request['id']));
		$template = $mysql->fetchRow();
		
		$list = new liste('Template: '.$template['name']);
		$list->add_columns(array(' ' => '50%', '' => ' '));
		$list->add_row(array(liste_title('ID'), $template['id']));
		$list->add_row(array(liste_title('Name'), $template['name']));
		$list->add_row(array(liste_title('Datei'), $template['path']));
		$list->add_row(array(liste_title('date'), date('d.m.Y', $template['date'])));
		$style->add($list->get());
		
		// Bearbeiten Button
		$form = new form();
		$form->button('Datensatz bearbeiten', $this->mode['edit_tpl_form']['link'].'&id='.$this->request['id']);
		$style->add($form->get());
	}
	
	/**
	 * Rechnung löschen
	 */
	function delete() {
		global $style, $mysql, $prefix;
		
		$mysql->delete($prefix.'bill', sql($this->request['id']));
		$style->box(p_icon().'Erfolgreich!', 'Die Rechnung wurde erfolgreich gelöscht!'.back_overview());
	}
	
	/**
	 * Rechnungstemplate löschen
	 */
	function delete_tpl() {
		global $mysql, $style, $prefix;
		
		$mysql->delete($prefix.'bill_template', sql($this->request['id']));
		$style->box(p_icon().'Erfolgreich!', 'Das Rechnungstemplate wurde erfolgreich gelöscht!'.back_overview());
	}
	
	/**
	 * Rechnung drucken
	 */
	function b_print() {
		global $mysql, $prefix, $style, $ajax;
		
		$mysql->select('id,name,path', $prefix.'bill_template');
		// Prüfen ob min. ein Template existiert
		if($mysql->count() > 0) {
			$form = new form();
			$form->new_group('Template');
			$options = array();
			while ($template = $mysql->fetchRow()) {
				$options[$template['path']] = $template['name'];
			}
			$form->select('Template:', 'template', $options);
			$form->input('', 'id', $this->request['id'], 'hidden');
			$form->button('Zum Ausdruck..', '', 'onclick="printBill();"');
			
			// Ajax
			$ajax->run();
			$style->add_js($ajax->getJS());
			$style->box('Drucken', $form->get());
		} else {
			$style->box(n_icon().'Fehler!', 'Es existieren noch keine Templates zu ausdrucken.'.back_overview());
		}
	}
	
	/**
	 * Rechnung Bezahlt
	 */
	function paid() {
		global $style;
		
		$this->setBillStatus($this->request['id'], 2); // 2 = Bezahlt
		$style->box(p_icon().'Erfolgreich!', 'Der Status der Rechnung wurde auf "Bezahlt" geändert.'.back_overview());
	}
	
	/**
	 * Übersicht
	 */
	function overview() {
		global $style, $mysql, $prefix;
		
		$list = new liste('Alle Rechnungen');
		$columns = array();
		$columns['ID'] = 30;
		$columns['Kundennummer'] = '';
		$columns['Rechnungsnummer'] = '';
		$columns['Betrag'] = '';
		$columns['Status'] = '';
		$columns['Optionen'] = '';
		$list->add_columns($columns);
		
		$changedStatusBills = array();
		$mysql->select('*', $prefix.'bill', 'ORDER BY status ASC');
		while ($bill = $mysql->fetchRow()) {
			$trueBillStatus = $this->checkBillStatus($bill['status'], $bill['date']);
			if ($trueBillStatus != $bill['status']) {
				// Status hat sich geändert
				$bill['new_status'] = $trueBillStatus;
				$changedStatusBills[] = $bill;
				
				// Status setzen
				$bill['status'] = $this->setBillStatus($bill['id'], $trueBillStatus);
			}
			
			$data = array();
			$data[] = $bill['id'];
			$data[] = $bill['kundennummer'];
			$data[] = $bill['rechnungsnummer'];
			$data[] = ($bill['betrag'].option('currency'));
			$data[] = $this->status[$bill['status']];
			$data[] = 	option_icon('edit', $this->mode['edit_form']['link'].'&id='.$bill['id']).
						option_icon('delete', $this->mode['delete']['link'].'&id='.$bill['id']).
						option_icon('show', $this->mode['show']['link'].'&id='.$bill['id']).
						link_icon('Drucken', 'printer', 'href="'.$this->mode['print']['link'].'&id='.$bill['id'].'"').
						($bill['status'] == 2 ? '' : link_icon('Bezahlt', 'bullet-green-alt', 'href="'.$this->mode['paid']['link'].'&id='.$bill['id'].'"'));
			$list->add_row($data);
		}
		
		// Statusänderungen mitteilung
		$count = count($changedStatusBills);
		if ($count > 0) {
			$text = 'Der Status von '.($count == 1 ? 'einer Rechnung ' : 'mehreren Rechnungen').' hat sich geändert:<br /><strong>';
			foreach ($changedStatusBills as $bill) {
				$text .= 'Rechnungsnr. '.$bill['rechnungsnummer'].' | ID: '.$bill['id'].' | Alter Status: '.$this->status[$bill['status']].' | Neuer Status: '.$this->status[$bill['new_status']].'<br />';
			}
			$style->box(w_icon().'Warnung!', $text.'</strong>');
		}
		
		$style->add($list->get());
		$style->add('<br /><br />');
		
		// Templateübersicht zum bearbeiten der Templates
		$liste = new liste('Templateübsersicht');
		$columns = array();
		$columns['ID'] = 30;
		$columns['Name'] = '';
		$columns['Datei'] = '';
		$columns['Optionen'] = '';
		$liste->add_columns($columns);
		$mysql->select('id,name,path', $prefix.'bill_template', 'ORDER BY id ASC');
		while ($tpl = $mysql->fetchRow()) {
			$data = array();
			$data[] = $tpl['id'];
			$data[] = $tpl['name'];
			$data[] = $tpl['path'];
			$data[] = 	option_icon('edit', $this->mode['edit_tpl_form']['link'].'&id='.$tpl['id']).
						option_icon('delete', $this->mode['delete_tpl']['link'].'&id='.$tpl['id']);
			$liste->add_row($data);
		}
		
		// Liste
		$style->add($liste->get());	
	}
	
	/**
	 * Überprüft den Status anhand des Zeitstempfels und gibt
	 * den aktuellen Status zurück
	 */
	function checkBillStatus($billStatus, $date) {
		if ($billStatus != 2) {
			// Tag in sekunden
			$tag_s = 86400;
			$time = time(); // heute
			
			// Fristen in bezug auf erstellungsdatum der Rechnung
			$frist1 = option('zahlungsfrist1')*$tag_s;
			$frist2 = (option('zahlungsfrist2')*$tag_s)+$frist1;
			$frist3 = (option('zahlungsfrist3')*$tag_s)+$frist1+$frist2;
			$frist4 = (option('zahlungsfrist4')*$tag_s)+$frist1+$frist2+$frist3;
			
			$diffTage = ($time-$date)/$tag_s;
			if ($diffTage <= $frist1) {
				return 3;
			} elseif ($diffTage <= $frist2) {
				return 4;
			} elseif ($diffTage <= $frist3) {
				return 5;
			} elseif ($diffTage <= $frist4) {
				return 6;
			}
		} else {
			return $billStatus;
		}
	}
	
	/**
	 * Setzte den Status der rechnung neu
	 */
	function setBillStatus($billId, $status) {
		global $mysql_func, $prefix;
		$update = array('status' => sql($status));
		$mysql_func->update($update, $prefix.'bill', sql($billId));
		return $status;
	}
}

$bill = new bill();
$bill->main();
?>