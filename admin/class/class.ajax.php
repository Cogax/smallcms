<?php
/**
 * 		Datei: 					admin/class.ajax.php
 * 		Erstellungsdatum:		28.12.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Ajax Klasse
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

class Ajax
{
	// Optionen f�r Ausgabe und Reloader bestimmen
	public $LiveXOptions = array('preloader' => 'pr', 'target' => 'result');
	
	// Speicherort f�r den JS Code der Funktion
	public $JSFunction = '';
	public $JSFunctions = array();
	
	// Speicherort f�r Ausgabe HTML Code
	public $resultHTMLStart = '<div id="result" align="center">';
	public $resultHTMLEnd = '</div>';
	
	// Speicherort f�r Preload HTML Code
	public $preloadHTML = '<div align="center"><span id="pr" style="visibility:hidden;"><img src="../images/admin/icons/ajax_loading.gif" /></span></div>';
	
	// Speichert die PHP Funktionsnamen
	public $PHPFunctions = array();
	
	// Speichert den PHPLiveX JS Code, der ganz am anfang angef�gt werden muss
	public $LiveXJS;
	
	// PHP Funktion Registrieren
	function registPHPFunction($FunctionName) {
		if(is_array($FunctionName)) {							// Pr�fen ob Array-Eingabe
			array_merge($this->PHPFunctions, $FunctionName);	// Arrays zusammenf�gen
		} else {
			$this->PHPFunctions[] = $FunctionName;				// Funktionsname in Array
		}
		return true;
	}
	
	// Eine Funktion hinzuf�gen mit JS Code
	function registJSFunction($JSFunctionCode) {
		$this->JSFunctions[] = $JSFunctionCode;
	}
	
	// Einen JS Code hinzuf�gen
	function registJSCode($JSCode) {
		$this->JSFunctions[] = $JSCode;
	}
	
	// PHPLiveX Starten
	function run() {
		ob_start(); 									// Buffer setzen
		$ajax = new PHPLiveX($this->PHPFunctions); 		// PHP Funktionen �bergeben
		$ajax->run(); 									// Ajax Starten
		$this->LiveXJS = ob_get_contents(); 			// Buffer zur�ckgeben. JS Code Speichern
		ob_clean(); 									// Buffer leeren
		return true;
	}
	
	// Ausgabe HTML code festlegen
	function setResult($resultHTMLStart, $resultHTMLEnd) {
		$this->resultHTMLStart = $resultHTMLStart;		// Setzen des Ausgabe HTML Start
		$this->resultHTMLEnd = $resultHTMLEnd;			// Setzen des Ausgabe HTML Ende
	}
	
	// Vorlade HTML Code festlegen
	function setPreload($preloadHTML) {
		$this->preloadHTML = $preloadHTML;				// Preload HTML Code festlegen
	}
	
	// Preload HTML zur�ckgeben
	function getPreload() {
		return $this->preloadHTML;						// Preload HTML zur�ckgeben
	}
	
	// Result HTML zur�ckgeben
	function getResult($startResultCode = '') {
		return $this->resultHTMLStart.$startResultCode.$this->resultHTMLEnd;
	}
	
	
	// Setzt die LiveX Options
	function setLiveXOptions($options) {
		$this->LiveXOptions = $options;
	}
	
	// Gibt den gesammten Javascript code zur�ck
	function getJS() {
		$js = '';
		$js .= $this->LiveXJS;
		$js .= '<script type="text/javascript">
		';
		foreach ($this->JSFunctions as $code) {
			$js .= $code;
		}
		$js .= '</script>';
		return $js;
	}
}
?>