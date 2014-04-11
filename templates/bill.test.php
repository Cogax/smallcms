<?php
/**
 * 		Datei: 					-
 * 		Erstellungsdatum:		-
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			-
 * 		Autor:					Andreas Gyr
 */
// TESTTEMPLATE
session_start();
error_reporting(E_ALL);

// Fpdf laden
require('../plugins/fpdf/fpdf.php');

// Daten empfangen
$data = unserialize(base64_decode($_SESSION['data']));
unset($_SESSION['data']);

// Rechnungsdaten
$bill = $data['bill'];

// Kundendaten
$customer = $data['customer'];

// Optionen
$option = array();
foreach ($data['option'] as $opt) {
	$option[$opt['name']] = $opt['value'];
}

/**
 * 	Verfügbare Variabeln
 */
// Rechnung
$bill['id'];						// Rechnungs ID
$bill['customer'];					// Kunden ID
$bill['kundennummer'];				// Kundennummer
$bill['rechnungsnummer'];			// Rechnungsnummer
$bill['date'];						// Erstellungsdatum
$bill['referenznummer'];			// Referenznummer
$bill['zahlungszweck'];				// Zahlungszweck
$bill['notizen'];					// Notizen
$bill['status'];					// Rechnungsstatus
$bill['rechnungsadresse'];			// Rechnungsadresse
$bill['template'];					// -- nothing
$bill['betrag'];					// Betrag
$bill['product_data'];				// Produktearray (serialisiert)

// Kunde
$customer['id'];					// Kunden ID
$customer['anrede'];				// Anrede
$customer['vorname'];				// Vorname
$customer['nachname'];				// Nachname
$customer['firma'];					// Firmenname
$customer['strasse'];				// Strasse und Hausnummer
$customer['zusatzzeile'];			// Zusatzzeile
$customer['plz'];					// PLZ
$customer['ort'];					// Ort
$customer['land'];					// Land
$customer['telefonnummer'];			// Tele. Nummer
$customer['mobiltelefonnummer'];	// Mobil Tele. Nummer
$customer['email'];					// Email Adresse
$customer['notizen'];				// Notizen
$customer['kundennummer'];			// Kundennummer
$customer['date'];					// Erstellungsdatum

// Optionen
$option['account_nr'];				// Eigene Kontonummer
$option['own_address'];				// Eigene Adresse
$option['bank_address'];			// Bank Adresse
$option['iban'];					// IBAN Nummer
$option['zahlungsfrist1'];			// Zahlungsfrist 1 Rechnung
$option['zahlungsfrist2'];			// Zahlungsfrist 2 Mahnung 1
$option['zahlungsfrist3'];			// Zahlungsfrist 3 Mahnung 2
$option['zahlungsfrist4'];			// Zahlungsfrist 4 mahnung 3
$option['currency'];				// Währung

/**
 * 	PDF Template
*/
class PDF extends FPDF
{

	//Kopfzeile
	function Header()
	{
	    // Logo
	    $this->Image('logo.jpg', 76, 8, 58, 18.520833333); // pfad, x (219px), y(70px), breite, höhe
	    // A4 Breite: 210 mm
	    // Angaben in mm!! px -> mm rechner: http://www.unitconversion.org/typography/pixels-x-to-millimeters-conversion.html
	    
	    // Schrifftart
	    $this->SetFont('Arial', 'i', 9);
	    
	    // Nach rechts aussen gehen
	    $this->SetXY(160, 8);
	    
	    // Webseite
	    $this->Cell(40, 9, 'www.cogax.ch', 0, 0, 'R');
	    
	    // Nach rechts aussen gehen
	    $this->SetXY(160, 13);
	    
	    // Email
	    $this->Cell(40, 9, 'info@cogax.ch', 0, 0, 'R');
	    
	    // Nach links aussen gehen
	    $this->SetXY(10, 6);
	    
	    // Addresse
	    $this->Cell(40, 9, 'Andreas Gyr', 0, 0, 'L');
	    
	     // Nach links aussen gehen
	    $this->SetXY(10, 11);
	    
	    // Addresse
	    $this->Cell(40, 9, 'Toggenburgerstr.3', 0, 0, 'L');
	    
	     // Nach links aussen gehen
	    $this->SetXY(10, 16);
	    
	    // Addresse
	    $this->Cell(40, 9, 'CH-9602 Bazenheid', 0, 0, 'L');
	    
	    // Zeilenumbruch
	    $this->Ln(50);
	}
	
	//Fusszeile
	function Footer()
	{
	    //Position 1,5 cm von unten
	    $this->SetY(-15);
	    //Arial kursiv 8
	    $this->SetFont('Arial','I',8);
	    //Seitenzahl
	    $this->Cell(0,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');   
	}
	
	//Colored table
	function FancyTable($header,$data)
	{
		// Globale variabeln
		global $option;
		
	    //Colors, line width and bold font
	    $this->SetFillColor(255, 255, 255);
	    $this->SetTextColor(0);
	    $this->SetDrawColor(0);
	    $this->SetLineWidth(.3);
	    $this->SetFont('Arial','B');
	    //Header
	    $w=array(100,22,35,33); // zusammen: 190
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],7,$header[$i],1,0,'C',1);
	    $this->Ln();
	    //Color and font restoration
	    $this->SetFillColor(216,216,216);
	    $this->SetTextColor(0);
	    $this->SetFont('');
	    //Data
	    $fill=0;
	    foreach($data as $row)
	    {
	        $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
	        $this->Cell($w[1],6,$row[1],'LR',0,'C',$fill);
	        $this->Cell($w[2],6,$row[2].$option['currency'],'LR',0,'C',$fill);
	        $this->Cell($w[3],6,$row[3].$option['currency'],'LR',0,'C',$fill);
	        $this->Ln();
	        $fill=!$fill;
	    }
	    $this->Cell(array_sum($w),0,'','T');
	}
}


//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Write(5, 'Sehr geehrter '.$customer['anrede'].' '.$customer['nachname'].',');
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Write(5, 'Ihre Rechnung (Rechnungsnr.: '.$bill['rechnungsnummer'].') setzt sich wie folgt zusammen:');
$pdf->Ln(6);
$pdf->Ln(6);
$header = array('Produkt','Menge','Preis pro Stück','Preis');
$products = unserialize($bill['product_data']);
$pdf->FancyTable($header,$products);
$pdf->Ln(6);
$pdf->SetFont('Arial', '');
$pdf->SetX(10);
// Total
$pdf->Cell(40, 10, 'Zahlungsfrist: 30 Tage', 0, 0, 'L');
// Nach rechts aussen gehen
$pdf->SetFont('Arial', 'B');
$pdf->SetX(160);
// Total
$pdf->Cell(40, 10, 'Total: '.$bill['betrag'].$option['currency'], 0, 0, 'R');
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->SetFont('Arial', '');
$pdf->Write(5, 'Vielen Dank für Ihren Auftrag!');

$pdf->Output();
?>