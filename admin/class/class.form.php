<?php
/**
 * 		Datei: 					class.form.php
 * 		Erstellungsdatum:		28.08.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Klasse für das erstellen der Formulare
 * 		Autor:					Andreas Gyr
 */

// $duty = Pflichtfelt - true/false
class form
{
	public $output = '';
	public $fieldset = 0;
	public $first = 1;
	public $duty = array();
	
	function __construct($action = '', $method = 'post', $code = '')
	{
		$this->output .= '<form name="form" action="'.$action.'" method="'.$method.'" '.$code.' onsubmit="javascript: return duty();" {enctype} class="niceform">';
	}
	
	function new_group($title) {
		if ($this->fieldset == 1) {
			$this->output .= '</fieldset>';
		}
		$this->output .= '<fieldset>
    	<legend>'.$title.'</legend>';
		$this->fieldset = 1;
		$this->first = 1;
		return true;
	}
	
	function request_hidden($mode = 'POST') {
		if ($mode == 'POST') {
			global $_POST;
			$arr = $_POST;
		} elseif ($mode == 'GET') {
			global $_GET;
			$arr = $_GET;
		}
		
		// Send löschen
		if(isset($arr['send'])) {
			unset($arr['send']);
		}
		
		
		foreach ($arr as $name => $value) {
			$this->input('', $name, $value, 'hidden', 32, 999999999, false);
		}
	}
	
	function editor($editor = 'ckeditor', $value = '') {
		$this->first = 0;
		$this->output .= '</fieldset>';

		// tinymce
		if ($editor == 'tinymce') {
			$this->output .= 
			'<label for="editor1">
				Text</label><br />
				<textarea id="editor1" class="editor" name="editor1" rows="15" style="100%;">'.htmlentities($value).'
			</textarea><br /><br />';
		}
		
		// openwysiwyg
		elseif($editor == 'openwysiwyg') {
			$this->output .= 
			'<label for="editor1">
				Text</label><br />
				<textarea id="editor1" class="editor" name="editor1" style="width:100%;height:200px;">'.htmlentities($value).'
			</textarea><br /><br />';
		}
		
		// textarea
		elseif($editor == 'textarea') {
			$this->output .= 
			'<label for="editor1">Text</label><br />
            <textarea name="editor1" id="editor1" rows="8" cols="120">'.htmlentities($value).'</textarea><br /><br />';
			
		}
		
		// ace
		elseif($editor == 'ace') {
			$this->output .= 
			'<label for="editor1">Code</label><br />
            <textarea name="editor1" id="editor1" style="width:100%;height:400px;" >'.htmlentities($value).'</textarea><br /><br />';
			
		}
		
		// ckeditor
		// if($editor == 'ckeditor')
		else {
			$this->output .= 
			'<label for="editor1">
				Text</label><br />
				<textarea class="editor" id="editor1" name="editor1" style="width:100%;height:200px;">'.htmlentities($value).'
			</textarea><br /><br />';
		}
		
	}
	
	function description($text) {
		if($this->first == 0) {
			$this->output .= '<br /><br /><br />';
		}
		$this->output .= $text;
		return true;
	}
	
	function input($beschriftung, $name, $value = '', $type = 'text', $size = 32, $maxlength = 250, $duty = true, $addToOutput = true) {
		$out = '';
		if ($duty) {
			$this->duty[$type][] = $name;
		}
		
		if($type == 'hidden') {
			$out .= '<input type="'.$type.'" name="'.$name.'" id="'.$name.'" size="0" value="'.$value.'" maxlength="'.$maxlength.'" />';
		} else {
			$this->first = 0;
			$out .= 
			'<dl>
	        	<dt><label for="'.$name.'">'.$beschriftung.'</label></dt>
	            <dd><input type="'.$type.'" name="'.$name.'" id="'.$name.'" size="'.$size.'" value="'.$value.'" maxlength="'.$maxlength.'" /></dd>
	        </dl>';
		}
		if($addToOutput) {
			$this->output .= $out;
		}
		return $out;
	}
	
	function select($beschriftung, $name, $options, $size = 1, $multiple = false, $selected = false, $addToOutput = true, $duty = true) {
		$this->first = 0;
		$out = '';
		
		if ($duty) {
			$this->duty['select'][] = $name;
		}
		
		$out .=
		'<dl>
        	<dt><label for="'.$name.'">'.$beschriftung.'</label></dt>
            <dd>
            	<select size="'.$size.'" name="'.$name.'" id="'.$name.'" '.($multiple == true ?  'multiple="multiple"' : '').'>';
                    
		foreach ($options as $value => $option) {
			$select = '';
			if($selected) {
				if(is_array($selected)) {
					if(in_array($value, $selected)) {
						$select = 'selected="selected" ';
					}
				} else {
					if($value == $selected) {
						$select = 'selected="selected" ';
					}
				}
			}
			$out .= '<option '.$select.'value="'.$value.'">'.$option.'</option>';
		}
		
		$out .=
            	'</select>
            </dd>
        </dl>';
        if($addToOutput) {
			$this->output .= $out;
		}
        return $out;
	}
	
	function checkbox($beschriftung, $name, $boxes, $checked = false, $addToOutput = true) {
		$this->first = 0;
		$out = '';
		
		$out .= '<dl>

        	<dt><label for="'.$name.'">'.$beschriftung.'</label></dt>
            <dd>';
		
		foreach ($boxes as $besch) {
			$check = '';
			if($checked) {
				if(is_array($checked)) {
					if(in_array($besch, $checked)) {
						$check = 'checked="checked"';
					}
				} else {
					if($besch == $checked) {
						$check = 'checked="checked';
					}
				}
			}
			$out .= '<input type="checkbox" '.$check.' name="'.$name.'" id="'.$name.'" /><label for="'.$besch.'" class="opt">'.$besch.'</label><br />';
		}
            
		$out .= '</dd></dl>';
		if($addToOutput) {
			$this->output .= $out;
		}
		return $out;
	}
	
	function radio($beschriftung, $name, $boxes, $checked = false, $addToOutput = true) {
		$this->first = 0;
		$out = '';
		$out .= '<dl>

        	<dt><label for="'.$name.'">'.$beschriftung.'</label></dt>
            <dd>';
		
		foreach ($boxes as $besch => $value) {
			$check = '';
			if($checked) {
				if($value == $checked) {
					$check = 'selected="selected"';
				}
			}
			$out .= '<input type="radio" '.$check.' name="'.$name.'" id="'.$value.'" value="'.$value.'" /><label for="'.$value.'" class="opt">'.$besch.'</label>';
		}
            
		$out .= '</dd></dl>';
		if($addToOutput) {
			$this->output .= $out;
		}
		return $out;
	}
	
	function textarea($beschriftung, $name, $value = '', $rows = 5, $cols = 55, $duty = true, $addToOutput = true) {
		$this->first = 0;
		$out = '';
		if ($duty) {
			$this->duty['textarea'][] = $name;
		}
		
		$out .= '<dl>
        	<dt><label for="'.$name.'">'.$beschriftung.'</label></dt>
            <dd><textarea name="'.$name.'" id="'.$name.'" rows="'.$rows.'" cols="'.$cols.'">'.$value.'</textarea></dd>
        </dl>';
		
		if($addToOutput) {
			$this->output .= $out;
		}
		return $out;
	}
	
	function submit($beschriftung = 'Absenden', $name = 'send') {
		$out = '';
		$out .= '</fieldset><fieldset class="action">
    	<input type="submit" name="'.$name.'" id="submit" value="'.$beschriftung.'" />
    </fieldset></form>';
	
		if(count($this->duty) > 0) {
			$i = 0;
			$js = '<script type="text/javascript">
					function duty() 
					{ 
						if(';
			foreach ($this->duty as $type => $dutys) {	
				foreach ($dutys as $duty_name) {
					if($i > 0) {
						$js .= ' || ';
					}
					if ($type == 'select') {
						$js .= 'document.forms["form"].elements["'.$duty_name.'"].selectedIndex == -1';
					} else {
						$js .= 'document.form.'.$duty_name.'.value == ""';
					}
				
					$i++;
				}
			}
			$js .= ') {
							alert(\'Du hast ein Feld vergessen auszufüllen!\');
							return false;
						}
						return true;
					}
				</script>';
			$out .= $js;
		}
		
		$this->output .= $out;
		return $out;
	}
	
	function file($beschriftung, $name) {
		$this->first = 0;
		
		$this->output = str_replace('{enctype}', 'enctype="multipart/form-data"', $this->output);
		$this->output .= '<dl>
        	<dt><label for="upload">'.$beschriftung.'</label></dt>
            <dd><input type="file" name="'.$name.'" id="upload" /></dd>
        </dl>';
	}
	
	function button($beschriftung, $link = '', $code = '') {
		$out = '';
		$out .= '<br /><br /><br /><button type="button" '.(empty($link) ? '' : 'onClick="self.location.href=\''.$link.'\'"').' '.(empty($code) ? '': $code).' name="button" id="button">'.$beschriftung.'</button>';
		$this->output .= $out;
		return $out;
	}
	
	function add_code($code, $first = 1) {
		$this->first = (bool) $first;
		$out = '';
		$out .= $code;
		$this->output .= $out;
		return $out;
	}

	function get() {
		$this->output = str_replace('{enctype}', '', $this->output);
		return $this->output;
	}
}
?>