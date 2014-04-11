<?php
/**
 * 		Datei: 					admin/class.tree.php
 * 		Erstellungsdatum:		10.11.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Erstellt die Tree ansichten
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

class tree
{
	public $output;
	public $add_counter = 0;
	public $tree_id;
	public $show_links = false;
	
	function __construct($tree_id = 'd') {
		$this->tree_id = $tree_id;
		$this->output .= 
		"<script type=\"text/javascript\">
		<!--
		".$tree_id." = new dTree('".$tree_id."');";
	}
	
	function add($beschriftung, $parent, $href = '', $open = false) {
		$this->output .= "
		".$this->tree_id.".add(".$this->add_counter.",".$parent.",'".(strlen($beschriftung) > 15 ? substr($beschriftung,0,15).'..' : $beschriftung)."','".$href."','','','','','".$open."');";
		$this->add_counter++;
		return $this->add_counter-1;
	}
	
	function show_links() {
		$this->show_links = true;
	}
	
	function get() {
		if($this->show_links) {
			$this->output = 
			'<a href="javascript: d.openAll();">Alle öffnen</a> | 
			<a href="javascript: d.closeAll();">Alle schliessen</a><br /><br />'.$this->output;
		}
		$this->output .=
		"
		document.write(".$this->tree_id.");
			//-->
		</script>";
		return '<div class="dtree">'.$this->output.'</div>';
	}
	
}
?>