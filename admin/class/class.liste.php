<?php
/**
 * 		Datei: 					class.liste.php
 * 		Erstellungsdatum:		28.08.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Klasse für das erstellen von Listen (Tabellen).
 * 		Autor:					Andreas Gyr
 */

class liste {
	
	public $output = '';
	public $columns;
	public $row_counter = 0;
	public $rows = '';
	public $table_id;
	
	function __construct($title) {
		$this->table_id = str_replace(array(':', ' ', ','), '', $title);
		global $style;
		$style->add_js('
		<script type="text/javascript">
		$(document).ready(function() 
    		{ 
    			$.tablesorter.defaults.widgets = [\'zebra\']; 
        		$("#'.$this->table_id.'").tablesorter(); 
   			} 
		); 
   		</script>');
		$this->output .=
		'<table class="list" width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td background="../images/admin/top.gif" height="26" valign="middle">
					<font class="list_titel">'.htmlentities($title).'</font>
				</td>
			  </tr>
			  <tr>
				<td>';
		return true;
	}
	
	function add_columns($columns) {
		$this->columns = count($columns);
		$this->output .=
		'<table height="20" id="'.$this->table_id.'" width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr height="20">';
		$i = 0;
		foreach ($columns as $title => $width) {
			$this->output .= '<th height="20" width="'.$width.'" background="../images/admin/top2.gif" class="list_column_title'.($i == 0 ? '_first' : '').'" valign="middle">&nbsp;&nbsp;'.htmlentities($title).'</th>';
			$i++;
		}
		$this->output .= '</tr></thead><tbody>';
		return true;
	}
	
	function add_row($data) {
		$this->output .= '<tr bgcolor="#E8E8E8">';
		foreach ($data as $value) {
			$this->output .= '<td height="20" class="list_column_text" valign="middle">&nbsp;&nbsp;'.$value.'</td>';
		}
		$this->output .= '</tr>';
		$this->row_counter++;
		return true;
	}
	
	function get() {
		// Keine Einträge
		if($this->row_counter == 0) {
			$this->output .= '<tr bgcolor="#FFFFFF">';
			$this->output .= '<td height="20" colspan="'.$this->columns.'" class="list_column_text" valign="middle">&nbsp;&nbsp;<em>'.htmlentities('Es existieren keine Einträge..').'</em></td>';
			$this->output .= '</tr>';
		}
		
		$this->output .= 
		'	</tbody></table>
					</td>
			  </tr>
			</table>';
		return $this->output;
	}
}
?>