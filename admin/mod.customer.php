<?php
/**
 * 		Datei: 					admin/mod.customer.php
 * 		Erstellungsdatum:		25.12.2010
 * 		Letzte bearbeitung:		14.10.2011
 * 		Beschreibung:			Kundenverwaltung
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

// neu erstellen link
$link = icon_link('Übersicht', 'table', INDEX);
$link .= icon_link('Kunden erstellen', 'user-group-add', INDEX.'&action=add');
$style->box('Optionen', $link, 'left');

if(formular('add')) {
	// Kunden erstellen - Formular
	$form = new form();
	$form->new_group('Email');
	$form->input('E-Mail*:', 'email');
	
	$form->new_group('Rechnungsadresse');
	$options = array();
	$options['Herr'] = 'Herr';
	$options['Frau'] = 'Frau';
	$options['Herr und Frau'] = 'Herr und Frau';
	$options['Familie'] = 'Familie';
	$form->select('Anrede*:', 'anrede', $options);
	$form->input('Vorname*:', 'vorname');
	$form->input('Nachname*:', 'nachname');
	$form->input('Firma:', 'firma', '', 'text', 32, 250, false);
	$form->input('Strasse und Hausnummer*:', 'strasse');
	$form->input('Zusatzzeile:', 'zusatzzeile', '', 'text', 32, 250, false);
	$form->input('PLZ*:', 'plz', '', 'text', 15, 5);
	$form->input('Ort*:', 'ort');
	$options = array();
	$options['Schweiz'] = 'Schweiz';
	$options['Lichtensteig'] = 'Lichtensteig';
	$options['Deutschland'] = 'Deutschland';
	$options['Österreich'] = 'Österreich';
	$form->select('Land*:', 'land', $options);
	
	$form->new_group('Telefon');
	$form->input('Telefonnr.*:', 'telefonnummer');
	$form->input('Mobiltelefonnr.:', 'mobiltelefonnummer', '', 'text', 32, 250, false);
	
	$form->new_group('Sonstiges');
	$form->textarea('Notizen:', 'notizen', '', 5, 60, false);
	$form->submit();
	
	$style->box('Neuen Kunden erstellen', $form->get());
} elseif (entry('add')) {
	// Kunden erstellen - Eintrag
	$insert = array();
	$insert['anrede'] = 				sql($_POST['anrede']);
	$insert['vorname'] = 				sql($_POST['vorname']);
	$insert['nachname'] = 				sql($_POST['nachname']);
	$insert['firma'] = 					sql($_POST['firma']);
	$insert['zusatzzeile'] = 			sql($_POST['zusatzzeile']);
	$insert['plz'] = 					sql($_POST['plz']);
	$insert['ort'] = 					sql($_POST['ort']);
	$insert['land'] = 					sql($_POST['land']);
	$insert['telefonnummer'] =			sql($_POST['telefonnummer']);
	$insert['mobiltelefonnummer'] = 	sql($_POST['mobiltelefonnummer']);
	$insert['email'] = 					sql($_POST['email']);
	$insert['notizen'] = 				sql($_POST['notizen']);
	$insert['kundennummer'] = 			sql(time());
	$insert['date'] = 					sql(time());
	$insert['strasse'] = 				sql($_POST['strasse']);
	$mysql->insert($insert, $prefix.'customer');
	$style->box(p_icon().'Erfolgreich!', 'Der Kunde wurde erfolgreich erstellt.'.back_overview());
} elseif (formular('edit')) {
	// Kunden bearbeiten - Formular
	$mysql->id_select('*', $prefix.'customer', sql($_GET['id']));
	$customer = $mysql->fetchRow();
	
	$form = new form();
	$form->new_group('Email');
	$form->input('E-Mail*:', 'email', $customer['email']);
	
	$form->new_group('Rechnungsadresse');
	$options = array();
	$options['Herr'] = 'Herr';
	$options['Frau'] = 'Frau';
	$options['Herr und Frau'] = 'Herr und Frau';
	$options['Familie'] = 'Familie';
	$form->select('Anrede*:', 'anrede', $options, 1, 0, $customer['anrede']);
	$form->input('Vorname*:', 'vorname', $customer['vorname']);
	$form->input('Nachname*:', 'nachname', $customer['nachname']);
	$form->input('Firma:', 'firma', $customer['firma'], 'text', 32, 250, false);
	$form->input('Strasse und Hausnummer*:', 'strasse', $customer['strasse']);
	$form->input('Zusatzzeile:', 'zusatzzeile', $customer['zusatzzeile'], 'text', 32, 250, false);
	$form->input('PLZ*:', 'plz', $customer['plz'], 'text', 15, 5);
	$form->input('Ort*:', 'ort', $customer['ort']);
	$options = array();
	$options['Schweiz'] = 'Schweiz';
	$options['Lichtensteig'] = 'Lichtensteig';
	$options['Deutschland'] = 'Deutschland';
	$options['Österreich'] = 'Österreich';
	$form->select('Land*:', 'land', $options, 1, 0, $customer['land']);
	
	$form->new_group('Telefon');
	$form->input('Telefonnr.*:', 'telefonnummer', $customer['telefonnummer']);
	$form->input('Mobiltelefonr.:', 'mobiltelefonnummer', $customer['mobiltelefonnummer'], 'text', 32, 250, false);
	
	$form->new_group('Sonstiges');
	$form->textarea('Notizen:', 'notizen', $customer['notizen'], 5, 60, false);
	$form->submit();
	
	$style->box('Kunden bearbeiten', $form->get());
} elseif (entry('edit')) {
	// Kunden bearbeiten - Eintrag
	$update = array();
	$update['anrede'] = 				sql($_POST['anrede']);
	$update['vorname'] = 				sql($_POST['vorname']);
	$update['nachname'] = 				sql($_POST['nachname']);
	$update['firma'] = 					sql($_POST['firma']);
	$update['zusatzzeile'] = 			sql($_POST['zusatzzeile']);
	$update['plz'] = 					sql($_POST['plz']);
	$update['ort'] = 					sql($_POST['ort']);
	$update['land'] = 					sql($_POST['land']);
	$update['telefonnummer'] =			sql($_POST['telefonnummer']);
	$update['mobiltelefonnummer'] = 	sql($_POST['mobiltelefonnummer']);
	$update['email'] = 					sql($_POST['email']);
	$update['notizen'] = 				sql($_POST['notizen']);
	$update['date'] = 					sql(time());
	$update['strasse'] = 				sql($_POST['strasse']);
	$mysql->update($update, $prefix.'customer', sql($_GET['id']));
	$style->box(p_icon().'Bearbeitung erfolgreich!', 'Der Kunde konnte erfolgreich bearbeitet werden!'.back_overview());
} elseif (delete()) {
	// löschen
	$mysql->delete($prefix.'customer', sql($_GET['id']));
	$style->box(p_icon().'Erfolgreich!', 'Der Kunde wurde erfolgreich gelöscht!'.back_overview());
} elseif (show()) {
	// Kunden anzeigen
	$mysql->id_select('*', $prefix.'customer', sql($_GET['id']));
	$customer = $mysql->fetchRow();
	$list = new liste('Kunde: '.$customer['kundennummer']);
	$list->add_columns(array('' => '50%', ' ' => ''));
	$list->add_row(array(liste_title('ID'), $customer['id']));
	$list->add_row(array(liste_title('Anrede'), $customer['anrede']));
	$list->add_row(array(liste_title('Vorname'), $customer['vorname']));
	$list->add_row(array(liste_title('Nachname'), $customer['nachname']));
	$list->add_row(array(liste_title('Firma'), $customer['firma']));
	$list->add_row(array(liste_title('Strasse'), $customer['strasse']));
	$list->add_row(array(liste_title('Zusatzzeile'), $customer['zusatzzeile']));
	$list->add_row(array(liste_title('PLZ'), $customer['plz']));
	$list->add_row(array(liste_title('Land'), $customer['land']));
	$list->add_row(array(liste_title('Telefonr.'), $customer['telefonnummer']));
	$list->add_row(array(liste_title('Mobiltelefonr.'), $customer['mobiltelefonnummer']));
	$list->add_row(array(liste_title('Email'), $customer['email']));
	$list->add_row(array(liste_title('Kundennr.'), $customer['kundennummer']));
	$list->add_row(array(liste_title('Notizen'), $customer['notizen']));
	$style->add($list->get());
	
	// Bearbeiten Button
	$form = new form();
	$form->button('Datensatz bearbeiten', INDEX.'&action=edit&id='.$_GET['id']);
	$style->add($form->get());
} elseif (view()) {
	// Kundenübersicht
	$list = new liste('Kundenübersicht');
	$columns = array();
	$columns['ID'] = 30;
	$columns['Vorname'] = '';
	$columns['Nachname'] = '';
	$columns['Email'] = '';
	$columns['Kundennummer'] = 30;
	$columns['Optionen'] = '';
	$list->add_columns($columns);
	
	$mysql->select('id,vorname,nachname,strasse,email,kundennummer', $prefix.'customer', 'ORDER BY id ASC');
	while ($customer = $mysql->fetchRow()) {
		$data = array();
		$data[] = $customer['id'];
		$data[] = $customer['vorname'];
		$data[] = $customer['nachname'];
		$data[] = $customer['email'];
		$data[] = $customer['kundennummer'];
		$data[] = 	option_icon('edit', INDEX.'&id='.$customer['id'])
					.option_icon('delete', INDEX.'&id='.$customer['id'])
					.option_icon('show', INDEX.'&id='.$customer['id']);
		$list->add_row($data);
	}
	$style->add($list->get());
}

// Modul Übersicht
$tree = new tree();
$tree->show_links();
$base = $tree->add('Modulkategorien', -1, ''); // Base
$res_cat = $mysql->select('*', $prefix.'modul_cat', 'ORDER by id ASC');
while ($cat = $mysql->fetchRow($res_cat)) {
	$parent = $tree->add($cat['name'], $base);
	$res_mod = $mysql->select('id,mod_name,mod_link', $prefix.'modul', 'WHERE mod_cat = '.sql($cat['id']));
	if($mysql->count() > 0) {
		while ($mod = $mysql->fetchRow($res_mod)) {
			$tree->add($mod['mod_name'], $parent, 'index.php?modul='.$mod['mod_link']);
		}
	}
}
$style->box('Modulübersicht', $tree->get(), 'left');
?>