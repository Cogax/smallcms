<?php
/**
 * 		Datei: 					class.liste.php
 * 		Erstellungsdatum:		28.08.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Klasse für das erstellen von Listen (Tabellen).
 * 		Autor:					Andreas Gyr
 */

class liste 
{
	
	public $output = '';
	public $columns;
	public $cols = array();
	public $row_counter = 0;
	public $rows = '';
	public $data = array();
	public $d = array();
	
	function __construct($title) {
		$this->output .=
		'<table class="list" id="'.$title.'" width="100%" border="0" cellspacing="0" cellpadding="0">
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
		'<table height="20" width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr height="20">';
		$i = 0;
		foreach ($columns as $title => $width) {
			$this->output .= '<td height="20" width="'.$width.'" background="../images/admin/top2.gif" class="list_column_title'.($i == 0 ? '_first' : '').'" valign="middle">&nbsp;&nbsp;'.htmlentities($title).'</td>';
			$this->data[$title] = array();
			$this->cols[$i] = $title;
			$i++;
		}
		$this->output .= '</tr>';
		return true;
	}
	
	function add_row($data) {
		foreach ($data as $key => $value) {
			$this->data[$this->cols[$key]][] = $value;
		}
	}
	
	function sort($col_title, $mode = SORT_ASC, $restlicheColTitles) {
		if (!is_array($restlicheColTitles))
			return false;
		
		if (!in_array($col_title, $this->cols))
			return false;
			
		
		switch (count($restlicheColTitles)) {
			case 1: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]]); break;
			case 2: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]]); break;
			case 3: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]]); break;
			case 4:		array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]]); break;
			case 5: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]]); break;
			case 6: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]], $this->data[$restlicheColTitles[5]]); break;
			case 7: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]], $this->data[$restlicheColTitles[5]], $this->data[$restlicheColTitles[6]]); break;
			case 8: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]], $this->data[$restlicheColTitles[5]], $this->data[$restlicheColTitles[6]], $this->data[$restlicheColTitles[7]]); break;
			case 9: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]], $this->data[$restlicheColTitles[5]], $this->data[$restlicheColTitles[6]], $this->data[$restlicheColTitles[7]], $this->data[$restlicheColTitles[8]]); break;
			case 10: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]], $this->data[$restlicheColTitles[5]], $this->data[$restlicheColTitles[6]], $this->data[$restlicheColTitles[7]], $this->data[$restlicheColTitles[8]], $this->data[$restlicheColTitles[9]]); break;
			case 11: 	array_multisort($this->data[$col_title], $mode, $this->data[$restlicheColTitles[0]], $this->data[$restlicheColTitles[1]], $this->data[$restlicheColTitles[2]], $this->data[$restlicheColTitles[3]], $this->data[$restlicheColTitles[4]], $this->data[$restlicheColTitles[5]], $this->data[$restlicheColTitles[6]], $this->data[$restlicheColTitles[7]], $this->data[$restlicheColTitles[8]], $this->data[$restlicheColTitles[9]], $this->data[$restlicheColTitles[10]]); break;
			default: echo 'Fuck OFF!!!';
		}
	}
	 
	function each_row() {
		
		$this->output .= '<tr bgcolor="'.($this->row_counter % 2 == 0 ? '#FFFFFF' : '#DDDDDD').'">';
		foreach ($this->data as $value) {
			$this->output .= '<td height="20" class="list_column_text" valign="middle">&nbsp;&nbsp;'.$value.'</td>';
		}
		$this->output .= '</tr>';
		$this->row_counter++;
		return true;
	}
	
	function get() {
		// Keine Einträge
		
		$this->sort('ID', SORT_DESC, array('Name', 'Wert', 'Beschreibung', 'Optionen'));
		print_r($this->data);
		$this->each_row();
		//print_r($this->data);
		if($this->row_counter == 0) {
			$this->output .= '<tr bgcolor="#FFFFFF">';
			$this->output .= '<td height="20" colspan="'.$this->columns.'" class="list_column_text" valign="middle">&nbsp;&nbsp;<em>'.htmlentities('Es existieren keine Einträge..').'</em></td>';
			$this->output .= '</tr>';
		}
		
		$this->output .= 
		'	</table>
					</td>
			  </tr>
			</table>';
		return $this->output;
	}
}
?>