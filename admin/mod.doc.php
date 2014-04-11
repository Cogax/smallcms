<?php
/**
 * 		Datei: 					admin/mod.documentation.php		
 * 		Erstellungsdatum:		10.10.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Dokumentation
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

class documentation {
	private $mode = array();
	
	function __construct() {
		$this->mode['1-1'] 			= setMode(array('step' => '1-1'), array('send' => false));
		$this->mode['1-2'] 			= setMode(array('step' => '1-2'), array('send' => false));
		$this->mode['1-3'] 			= setMode(array('step' => '1-3'), array('send' => false));
		$this->mode['2-1'] 			= setMode(array('step' => '2-1'), array('send' => false));
		$this->mode['2-2'] 			= setMode(array('step' => '2-2'), array('send' => false));
		$this->mode['2-3'] 			= setMode(array('step' => '2-3'), array('send' => false));
		
	}
	
	function main() {
		$this->tree_box();
		
		switch (true) {
			case $this->mode['1-1']['check']:		$this->vor_installation(); 		break;
			case $this->mode['1-2']['check']:		$this->installation(); 			break;
			case $this->mode['1-3']['check']:		$this->nach_installation(); 	break;
			case $this->mode['2-1']['check']:		$this->tpl_einfuehrung(); 		break;
			case $this->mode['2-2']['check']:		$this->tpl_vorbereitung(); 		break;
			case $this->mode['2-3']['check']:		$this->tpl_hinzufuegen(); 		break;
		}
	}
	
	/**
	 * Baumstrukturbox
	 */
	function tree_box() {
		global $style;
		// ----- Modul �bersicht
		$tree = new tree();
		$tree->show_links();
		$base 			= $tree->add('Dokumentation', -1, ''); // Base
		$install 		= $tree->add('Installation', $base);
			$tree->add('Vor der Installation', $install, $this->mode['1-1']['link']);
			$tree->add('Die Installation', $install, $this->mode['1-2']['link']);
			$tree->add('Nach der Installation', $install, $this->mode['1-3']['link']);
		$template 		= $tree->add('Templates', $base);
			$tree->add('Einf�hrung ', $template, $this->mode['2-1']['link']);
			$tree->add('Vorbereitung', $template, $this->mode['2-2']['link']);
			$tree->add('Hinzuf�gen', $template, $this->mode['2-3']['link']);
		$artikel 		= $tree->add('Artikel', $base);
			$tree->add('Aufbau eines T', $artikel, $this->mode['1-1']['link']);
			$tree->add('Aufbau eines', $artikel, $this->mode['1-1']['link']);
			$tree->add('Aufbau einess', $artikel, $this->mode['1-1']['link']);
			$tree->add('Aufbau etes', $artikel, $this->mode['1-1']['link']);
		$seiten 		= $tree->add('Seiten', $base);
			$tree->add('Aufbau eine', $seiten, $this->mode['1-1']['link']);
			$tree->add('Aufbau eins', $seiten, $this->mode['1-1']['link']);
			$tree->add('Aufbau eines', $seiten, $this->mode['1-1']['link']);
			$tree->add('Aufbau einess', $seiten, $this->mode['1-1']['link']);
		$einstellungen 	= $tree->add('Einstellungen', $base);
			$tree->add('Aufbau eines s', $einstellungen, $this->mode['1-1']['link']);
			$tree->add('Aufbau eines', $einstellungen, $this->mode['1-1']['link']);
			$tree->add('Aufbau eine', $einstellungen, $this->mode['1-1']['link']);
		$style->box('�bersicht', $tree->get(), 'left');
	}
	
	function vor_installation() {
		global $style;
		$text = 'Setzten sie f�r die Verzeichnise admin/, admin/upload/, backup/ sowie f�r die Datei
				conf.config.php die CHMOD Rechte auf 777.';
		$style->box('Vor der Installation', $text);
	}
	
	function installation() {
		global $style;
		$text = 'Als erstes m�ssen Sie die MySQL Daten angeben. Der Hostname ist in den meisten f�llen der vorgegebene
		"localhost". Geben Sie Ihren Datenbankname an, in welche die smallCMS Daten gespeichert werden sollen. Dann noch
		Benutzername und Passwort des MySQL Accounts.<br />Bei Pr�fix k�nnen Sie das Pr�fix f�r die MySQL Tabellen bestimmen.
		Standartm�ssig einfach den vorgegebenen Wert nehmen.<br /><br />
		Bei den n�chsten Formularfeldern k�nnen Sie die Daten f�r Ihren htaccess Account bestimmen. Der htaccess Account ist nichts
		anderes als der Account, welchen Sie ben�tigen um sich im Administrator Panel anzumelden. Geben Sie also einen Benutzernamen sowie ein 
		dazugeh�riges Passwort ein.<br />Mit einem klick auf den "Installieren" Button wird das smallCMS selbst�ndig Installiert. Bei
		Fehlern melden Sie sich bitte an <a href="mailto:info@cogax.ch">Cogax</a>';
		$style->box('Die Installation', $text);
	}
	
	function nach_installation() {
		global $style;
		$text = 'Nach der Installation m�ssen Sie dem Verzeichnis admin/ umbedingt wieder die Dateirechte auf
		CHMOD 755 zur�cksetzen! Zudem sollten Sie das Verzeichnis install/ nun komplet l�schen, damit keine Zweitinstallation
		durchgef�hrt werden kann.';
		$style->box('Nach der Installation', $text);
	}
	
	function tpl_einfuehrung() {
		global $style;
		$text = 'Ein Template ist eigendlich nichts anderes als der HTML (auch XHTML etc.) Code Ihres Webdesigns. Der einzige unterschied
		ist, dass bei einem Template anstelle des Inhalts (Text, Bilder etc.) nur Platzhalter gesetzt werden. Das Template wird nicht wie 
		eine gew�hnliche HTML Datei mit der Format .html abgespeichert, sondern mit dem Format .tpl';
		$style->box('Templates: Einf�hrung', $text);
	}
	
	function tpl_vorbereitung() {
		global $style;
		$text = 'Wenn Sie den HTML Code Ihres Webdesign haben m�ssen Sie nun �berall wo sp�ter ein (Dynamischer-)Inhalt wie Texte, Links, Bilder etc.
		erscheinen soll einen Platzhalter setzten. Dies tun Sie standartgemass so: {P}Panelname{/P}. Dazu ein kleines Beispiel:<br />
		An den Ort im HTML Code wo Sie z.B nun Ihre Navigation haben, schreiben Sie anstatt den HTML Code f�r die Navigation einfach "{P}Navigation{/P}" hin.
		Oder dort wo sp�ter Ihr Inhalt erscheinen soll schreiben Sie {P}Inhalt{/P}.<br />Vergewissern Sie sich, dass Sie wirklich �berall dort Platzhalter
		gesetzt haben, wo sp�ter ein dynamischer Inhalt erscheinen soll. Anschliessend k�nnen Sie Ihr Template im Format .tpl speichern (z.b. "webdesign.tpl").
		Dieses Template m�ssen Sie nun (per FTP) in das Verzeichnis templates/ laden.<br /><br />
		Sie k�nnen die Tags ({P} und {/P}) dieser Platzhalter auch �ndern - sp�ter mehr dazu (Einstellungen). ';
		$style->box('Vorbereitung eines Template', $text);
	}
	
	function tpl_hinzufuegen() {
		global $style;
		$text = 'Gehen Sie nun im Administrations Panel auf "Templates" (obere Navigation) und klicken Sie auf der linken Seite auf
		"Neues Template". Sie k�nnen nun Ihren Template einen Namen geben (z.B. "Webdesign1"). Darunter k�nnen Sie das zuvor raufgeladene
		Template ausw�hlen. Mit einem klick auf der "Template Hinz�f�gen" Button wird das Template Installiert.';
		$style->box('Hinzuf�gen eines Template', $text);
	}
}

$doku = new documentation();
$doku->main();
?>