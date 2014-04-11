<?php
/**
 * 		Datei: 					admin/mod.modul.php
 * 		Erstellungsdatum:		26.09.2010
 * 		Letzte bearbeitung:		07.09.2011
 * 		Beschreibung:			Verwaltung der Kategorien für die Zusatzmodule
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');
require_once('../plugins/pclzip/pclzip.lib.php'); // ZIP Packer

/**
 * Nex Version:
 * 					- Bei Fehler im Update wird automatisch wieder das Backup eingespielt
 * 					- Modul delete
 * 					- sql backup
 */

class modul
{	
	private $mode = array();
	private $request = array();
	
	public $mod_format;
	public $mod_filename;
	public $mod_name;
	public $mod_version;
	public $mod_update = 0;
	public $mod_info = array();
	public $mod_update_id;
	
	function __construct($request = false) {
		// Modes setzen
		$this->mode['install_form'] 		= setMode(array('action' => 'add'), array('send' => false));
		$this->mode['install'] 				= setMode(array('action' => 'install'), array('send' => true));
		$this->mode['show']					= setMode(array('action' => 'show'), array('send' => false));
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
			case $this->mode['install_form']['check']:
				$this->upload_form();
				break;
				
			case $this->mode['install']['check']:
				$this->install();
				break;
				
			case $this->mode['show']['check']:
				$this->show();
				break;
			
			default:
				$this->overview();
				break;
		}
	}
	
	/**
	 * Optionenbox
	 */
	function option_box() {
		global $style;
		// ---- Neue Modulktegorie erstellen link
		$link = icon_link('Übersicht', 'table', $this->mode['overview']['link']);
		$link .= icon_link('Modul installieren', 'notepad-add', $this->mode['install_form']['link']);
		$style->box('Optionen', $link, 'left');
	}
	
	/**
	 * Baumstrukturbox
	 */
	function tree_box() {
		global $style, $mysql, $prefix;
		// ----- Modul Übersicht
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
	}
	
	/**
	 * Modul Upload Formular
	 */
	function upload_form() {
		global $style;
		
		$form = new form($this->mode['install']['link']);
		$form->new_group('Zip Archiv');
		$form->file('Datei:', 'thefile');
		$form->submit('Installieren');
		$style->box('Modul Installieren', $form->get());
	}
	
	/**
	 * Installiert das Modul
	 */
	function install() {
		global $style;
		
		$file = $_FILES['thefile'];
		
		try {
			$this->check_archive($file);// Zip Archiv prüfen
			$this->check_version(); // Modul schon vorhanden? Version prüfen!
			$this->sql_backup(); // SQL backup
			$this->upload($file); // Datei heraufladen
			$this->extract();// Daten entpacken / löschen fals update
			$this->install_sql();// SQL ausführen
			if ($this->mod_update) {
				// Update Erfolgreich
				$style->box(p_icon().'Erfolgreiches Update!', 'Das Modul "'.$this->mod_name.'" wurde erfolgreich aktualisiert!'.back_overview());
			} else {
				// Neuinstallation erfolgreich
				$style->box(p_icon().'Erfolgreiche Installation!', 'Das Modul "'.$this->mod_name.'" wurde erfolgreich installiert!'.back_overview());
			}
		} catch (Exception $e) {
			$style->box(n_icon().'Fehler!', $e->getMessage().back_mode($this->mode['install_form']['link']));
		}
	}
	
	/**
	 * Prüft ob das ZIP archiv
	 */
	function check_archive($file) {
		$this->mod_filename		= $file['name'];
		$this->mod_format 		= substr($file['name'], -4);
		$this->mod_name 		= substr($file['name'], 0, strpos($file['name'], '_'));
		$this->mod_version 		= str_replace(array($this->mod_format, $this->mod_name, '_'), '', $file['name']);
		
		// Format prüfen
		if ($file['type'] != 'application/zip' || $this->mod_format != '.zip')
			throw new Exception('Die ausgwählte Datei ist keine Zip Datei!');
			
		// grösse prüfen
		//if ($file['size'] > 10000000)
		//	throw new Exception('Die ausgwählte Datei ist zu gross!');
		
		return true;	
	}
	
	/**
	 * Prüft ob das Modul schon istalliert ist und vergleicht die Versionen
	 */
	function check_version() {
		global $mysql, $prefix;
		
		// Modul schon vorhanden?
		$mysql->select('id, mod_name, mod_version', $prefix.'modul', 'WHERE mod_name = '.sql($this->mod_name));
		$mod = $mysql->fetchRow();
		if ($mysql->count() > 0) {
			// Versionen vergleichen
			if ($mod['mod_version'] >= $this->mod_version) {
				throw new Exception('Die Version inst veraltet oder gleich alt wie der aktuelle Version!');
			} else {
				$this->mod_update = 1; // Modul muss aktualisiert werden
				$this->mod_update_id = $mod['id'];
			}
		}
		
		return true;
	}
	
	function sql_backup() {
		// to be continued.. ;)
	}
	
	/**
	 * Dateibackup bei Update
	 */
	function backup($file_list) {
		// Verzeichnis erstellen
		$dir = '../backup/'.date('d_m_Y_H_i_s', time());
		if (!mkdir($dir, 0700))
			throw new Exception('Das Backupverzeichnis konnte nicht angelegt werden!');
		
		foreach ($file_list as $file) {
			if ($file['folder']) {
				// Verzeichnis erstellen
				if (!mkdir($dir.'/'.$file['filename'], 0700))
					throw new Exception('Das Verzeichnis '.$dir.'/'.$file['filename'].' konnte nicht angelegt werden!');
			} else {
				// Datei kopieren
				if(!copy('../'.$file['filename'], $dir.'/'.$file['filename']))
					throw new Exception('Die Datei '.$file['filename'].' konnte nicht kopiert werden');	
					
				if(!unlink('../'.$file['filename']))
					throw new Exception('Die Datei ../'.$file['filename'].' konnte nicht gelöscht werden!');		
			}
		}
		
		return true;
	}
	
	/**
	 * Lädt die Datei hoch
	 */
	function upload($file) {
		if(!move_uploaded_file($file['tmp_name'], 'upload/'.$file['name']))
			throw new Exception('Upload Fehlgeschlagen!');
			
		return true;
	}
	
	/**
	 * Entpackt die Datei
	 */
	function extract() {
		// Entpacken und auflisten
		$zip = new PclZip('upload/'.$this->mod_filename);
		if (($list = $zip->listContent()) == 0) {
			throw new Exception("Error : ".$zip->errorInfo(true));
		}
//		print_r($list); // zeigt inhalt des uploads
		
		// Backup machen, falls Update
		if ($this->mod_update) {
			$this->backup($list);
		}
		
		// Dateien an richtigen ort entpacken
		if ($zip->extract(PCLZIP_OPT_PATH, '../') == 0) 
			throw new Exception("Error : ".$zip->errorInfo(true));
		
		// Info Datei laden
		if(!include('info/info.'.strtolower($this->mod_name.'.php'))) 
			throw new Exception('Die Info Datei konnte nicht geladen werden!');
			
		// Info Daten laden
		$this->mod_info = $info;
			
		return true;
	}
	
	/**
	 * Prüft ob die Vorhandenen Module schon installiert sind
	 */
	function check_need_mod() {
		
	}
	
	/**
	 * Führt die SQL Befehle durch
	 */
	function install_sql() {
		global $mysql, $prefix;
		$mysql_query = array();
		
		// SQL Dateien laden
		if(is_array($this->mod_info['sql_file'])) {
			foreach ($this->mod_info['sql_file'] as $sql_file) {
				// SQL File includen
				if(!include('../'.$sql_file))
					throw new Exception('SQL File "'.$sql_file.'" konnte nicht geladen werden!');
					
				// Querys speichern
				if (isset($query))
					$mysql_query = array_merge($mysql_query, $query);
			}
		} else {
			// SQL File includen
			if(!include('../'.$this->mod_info['sql_file']))
				throw new Exception('SQL File "'.$this->mod_info['sql_file'].'" konnte nicht geladen werden!');
				
			// Querys speichern
			if (isset($query)) {
				$mysql_query = $query;
			}
		}
		
		// Querys ausführen
		$i = 1;
		foreach ($mysql_query as $key => $sql) {
			if(!$mysql->query($sql))
				throw new Exception('Fehler bei MySQL Query Nr. '.$i);
				
			$i++;
		}
		
		// Modul eintragen
		if (!$this->save_infos()) 
			throw new Exception('Fehler: Modul konnte nicht korrekt eingetragen werden (MySQL)!');
		
		return true;
	}
	
	/**
	 * Speichert die Modulinformationen
	 */
	function save_infos() {
		global $mysql, $prefix;
		
		// ID der Modulkategorie raussuchen
		$mysql->select('id', $prefix.'modul_cat', 'WHERE name = '.sql($this->mod_info['mod_cat']));
		if ($mysql->count() == 1) {
			$mod_cat = $mysql->fetchRow();
			$this->mod_info['mod_cat'] = $mod_cat['id'];
		} else {
			// Falls keine Modulkategorie mit diesem namen existiert ist Modulkategorie = 0!
			$this->mod_info['mod_cat'] = '0';
		}
		
		// ID der Navigationskategorie raussuchen
		$mysql->select('id', $prefix.'admin_navigation_cat', 'WHERE name = '.sql($this->mod_info['navigation_cat']));
		if ($mysql->count() == 1) {
			$nav_cat = $mysql->fetchRow();
			$this->mod_info['navigation_cat'] = $nav_cat['id'];
		} else {
			// Falls keine Navigationskategorie mit diesem namen existiert ist Navigationskategorie = 0!
			$this->mod_info['navigation_cat'] = '0';
		}
		
		// Pfad richtig machen
		$this->mod_info['mod_file'] = str_replace('admin/', '', $this->mod_info['mod_file']);
		
		// Veröffentlichungsdatum in Timestamp wandeln
		$day = substr($this->mod_info['mod_date'], 0, 2);
		$month = substr($this->mod_info['mod_date'], 3, 2);
		$year = substr($this->mod_info['mod_date'], 6, 4);
		$this->mod_info['mod_date'] = mktime(0, 0, 0, $month, $day, $year);
		
		$insert = array();
		foreach ($this->mod_info as $key => $value) {
			$insert[$key] = sql($value);
		}
		
		if ($this->mod_update) {
			// Update Modinfos
			$mysql->update($insert, $prefix.'modul', sql($this->mod_update_id));
		} else {
			// Neue Modinfos
			$mysql->insert($insert, $prefix.'modul');
		}
		return true;
	}
	
	/**
	 * Zeigt die Informationen des Modul an
	 */
	function show() {
		global $style, $mysql, $prefix;
		// Modul anzeigen
		$mysql->id_select('*', $prefix.'modul', sql($this->request['id']));
		$mod = $mysql->fetchRow();
		
		$list = new liste('Modul: '.$mod['mod_name']);
		$list->add_columns(array('' => '50%', ' ' => ''));
		$list->add_row(array(liste_title('ID'), $mod['id']));
		$list->add_row(array(liste_title('Modulname'), $mod['mod_name']));
		$list->add_row(array(liste_title('Modulkategorie'), $this->get_modul_cat_name($mod['mod_cat'])));
		// navigationskategorie noch machen<=========================================================
		$list->add_row(array(liste_title('Schlüssel'), $mod['mod_key']));
		$list->add_row(array(liste_title('Linkparameter'), $mod['mod_link']));
		$list->add_row(array(liste_title('Paket'), $mod['mod_archive']));
		$list->add_row(array(liste_title('Version'), $mod['mod_version']));
		$list->add_row(array(liste_title('Autor'), $mod['mod_author']));
		$list->add_row(array(liste_title('Copyright'), $mod['mod_copyright']));
		$list->add_row(array(liste_title('Veröffentlichungsdatum'), date('d.m.Y', $mod['mod_date'])));
		$list->add_row(array(liste_title('Changelog'), $mod['mod_changelog']));
		$list->add_row(array(liste_title('Beschreibung'), $mod['mod_description']));
		$style->add($list->get());
		
		// Bearbeiten Button
		$form = new form();
		$form->button('Zurück', INDEX);
		$style->add($form->get());
	}
	
	/**
	 * Modulübersicht
	 * 
	 *  	Next Version: 		- Falls Modul Vorhanden sind, welche keiner Modulkategorie zugeordnet sind,
	 * 							  dann sollten diese in einer seperaten liste auch aufgelistet werden und evtl. 
	 * 							  noch speziell markiert werden!
	 */
	function overview() {
		global $mysql, $prefix, $style;
		
		// Kategorieübersicht mit Modulen
		$res_cat = $mysql->select('*', $prefix.'modul_cat', 'ORDER BY id ASC');
		while($cat = $mysql->fetchRow($res_cat)) {
			// Kateogireübersicht für bearbeitung
			$liste = new liste($cat['name']);
			$columns = array();
			$columns['ID'] = 30;
			$columns['Name'] = '';
			$columns['Linkname'] = '';
			$columns['Veröffentlichung'] = '';
			$columns['Optionen'] = '';
			$liste->add_columns($columns);
			
			$res_mod = $mysql->select('*', $prefix.'modul', 'WHERE mod_cat = '.sql($cat['id']));
			while($mod = $mysql->fetchRow($res_mod)) {
				$data = array();
				$data[] = $mod['id'];
				$data[] = '<strong><a style="color:#15428B;" href="index.php?modul='.$mod['mod_link'].'">'.$mod['mod_name'].'</a></strong>';
				$data[] = $mod['mod_link'];
				$data[] = date("d.m.Y", $mod['mod_date']);
				$data[] = option_icon('show', 'index.php?modul=modul&id='.$mod['id']);
				$liste->add_row($data);
			}
			$style->add($liste->get().'<br /><br />');
		}
	}
	
	/**
	 * Gibt den namen der Modulkategorie zurück
	 */
	static function get_modul_cat_name($cat_id) {
		global $mysql, $prefix;
		
		return $mysql->get_from_id('name', $prefix.'modul_cat', sql($cat_id));
	}
	
}
$mod = new modul();
$mod->main();
?>