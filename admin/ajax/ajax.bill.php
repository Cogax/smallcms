<?php
session_start();
/**
 * 		Datei: 					admin/ajax/ajax.bill.php
 * 		Erstellungsdatum:		30.12.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Ajax Funktionen für mod.bill.php
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

/**
 * 		PHP FUNCTIONS
 */
// Gibt die aktuelle Liste der gewählten Produkte zurück.
function showProducts($info) {
	// Globale Variablen
	global $mysql, $prefix;
	$total_preis = 0;
	
	// Aktuelle Produkteliste zusammenstellen
	$list = new liste('Produkteübersicht');
	$columns = array();
	$columns['Produkt'] = '';
	$columns['Menge'] = 60;
	$columns['Preis pro Stück'] = '';
	$columns['Preis'] = '';
	$columns['Del'] = '';
	$list->add_columns($columns);
	
	foreach ($info->products as $ke => $product_id) {
		$menge = $info->menges[$ke];
		$key = $info->keys[$ke];
		// Wenn ein Product glöscht wurde, so ist die Product_id = 'n'. Wenn ein Array element
		// gelöscht wird, so verschieben sich die keys, darum wird der Inhalt mit n ersetzt.
		if ($product_id != 'n') {
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
	// Total Preis
	$data = array();
	$data[] = '<strong>Preis Total:</strong>';
	$data[] = '';
	$data[] = '';
	$data[] = '<strong>'.((int) $total_preis).option('currency').'</strong>';
	$data[] = '';
	$list->add_row($data);
	
	// Hidden formularfelder für übergabe
	$hidden = 	'<input type="hidden" name="products" value="'.base64_encode(serialize($info->products)).'" />
				<input type="hidden" name="menges" value="'.base64_encode(serialize($info->menges)).'" />
				<input type="hidden" name="keys" value="'.base64_encode(serialize($info->keys)).'" />
				<input type="hidden" name="preis_total" value="'.$total_preis.'" />';
	
	return $list->get().$hidden;
}
// PHP Funktion showProducts registrieren
$ajax->registPHPFunction('showProducts');

function getBillData($info) {
	// Globale Variabeln
	global $mysql, $prefix;
	$data = array();
	
	// Rechnungsdaten holen
	$mysql->id_select('*', $prefix.'bill', sql($info->id));
	$data['bill'] = $mysql->fetchRow();
	
	// Produkte
	$products = array();
	$p = unserialize($data['bill']['product_data']);
	foreach ($p as $key => $arr) {
		foreach ($arr as $product_id => $menge) {
			$products[$key][] = $mysql->get_from_id('name', $prefix.'product', sql($product_id)); // Produktname
			$products[$key][] = $menge;
			$products[$key][] = $mysql->get_from_id('price', $prefix.'product', sql($product_id));
			$products[$key][] = ($products[$key][1] * $products[$key][2]);
		}
	}
	unset($data['bill']['product_data']);
	$data['bill']['product_data'] = serialize($products);
	
	// Kunden auslesen
	$mysql->id_select('*', $prefix.'customer', sql($data['bill']['customer']));
	$data['customer'] = $mysql->fetchRow();

	// Optionen Rechnungsverwaltung & Produkteverwaltung
	$res_cat = $mysql->select('id', $prefix.'option_cat', "WHERE name = 'Rechnungsverwaltung' OR name = 'Produktverwaltung'");
	while($cat = $mysql->fetchRow($res_cat)) {
		// Optionen aus der Kategorie lesen
		$res = $mysql->select('id,name,value', $prefix.'option', "WHERE cat = ".sql($cat['id']));
		while ($option = $mysql->fetchRow($res)) {
			$data['option'][] = $option;
		}
	}
	
	$übergabe = base64_encode(serialize($data));
	$_SESSION['data'] = $übergabe;
}
// PHP Funktion getBillData registrieren
$ajax->registPHPFunction('getBillData');

/**
 * 		JS FUNCTIONS
 */

// Fügt ein produkt hinzu und speichert dieses in einem JS Array.
// Gibt dieses Array an die PHP Funktion 'showProducts' weiter.
$JS_Function_addProduct = 
'var products = new Array();
var menges = new Array();
var keys = new Array();
var xyz = 0;
function addProduct() {
	var menge = document.getElementById("menge").value;
	var product = document.getElementById("product").value;
	products[xyz] = product;
	menges[xyz] = menge;
	keys[xyz] = xyz;
	xyz++;
	var info = {"products": products, "menges": menges, "keys": keys};
	showProducts(info, {"preloader": "pr", "target": "result"});
}';
// JS Funktion addProduct registrieren
$ajax->registJSFunction($JS_Function_addProduct);

// Löscht ein Produkt aus dem JS Array (anstatt es zu lschen wird der inhalt
// des Elementes mit 'n' ersetzt, da sich sonst die keys
// verschieben). Danach wird aktuelles Produkte Array
// der PHP Funktion 'showProducts' übergeben.
$JS_Function_delProduct = '
function delProduct(key) {
	products.splice(key, 1, "n");
	menges.splice(key, 1, "n");
	keys.splice(key, 1, "n");
	var info = {"products": products, "menges": menges, "keys": keys};
	showProducts(info, {"preloader": "pr", "target": "result"});
}';
// JS Funktion delProduct registrieren
$ajax->registJSFunction($JS_Function_delProduct);

// Öffnet eine Rechnung im entsprechendem Template.
// Dabei wird der Pfad mitgeliefert. Die Rechnungs und Kundendaten werden
// in der PHP Funktion getBillData geholt.
$JS_Function_printBill = '
function printBill() {
	var path = document.getElementById("template").value;
	var id = document.getElementById("id").value;
	var info = {"id": id};
	getBillData(info, {"onFinish": function() {
			druckFenster = window.open("'.option('http_path').'templates/"+path, "Rechnung Drucken");
			druckFenster.focus();
		}
	});
}';
// JS Funktion printBill registrieren
$ajax->registJSFunction($JS_Function_printBill);
?>