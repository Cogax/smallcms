<?php
/**
 * 		Datei: 					class.style.php
 * 		Erstellungsdatum:		28.08.10
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Klasse für das Backend Design
 * 		Autor:					Andreas Gyr
 */

class style
{
	public $output = '';
	public $panel = array();
	public $end = '';
	public $js_include = '';
	public $js_files = array();
	
	function __construct($page_title = 'smallCMS') {
		// Header
		$this->output .= 
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>'.$page_title.'</title>
		<link rel="stylesheet" type="text/css" href="../templates/admin/style.css" />
		<script language="javascript" type="text/javascript" src="../plugins/phplivex/phplivex.js"></script> <!-- PHPLiveX -->
		<script language="javascript" type="text/javascript" src="../templates/admin/niceforms.js"></script>
		<style type="text/css" media="screen">@import url(../templates/admin/niceforms-default.css);</style>
		<link rel="StyleSheet" href="../templates/admin/dtree.css" type="text/css" />
		<script type="text/javascript" src="../templates/admin/dtree.js"></script>
		<script type="text/javascript" src="../templates/admin/print_r.js"></script>
		<script type="text/javascript" src="../templates/admin/serialize.js"></script>
		<script type="text/javascript" src="../templates/admin/jquery-1.6.4.min.js"></script> 
		<script type="text/javascript" src="../templates/admin/jquery.tablesorter.min.js"></script> 
		<script type="text/javascript" src="../templates/admin/jquery.tablesorter.pager.js"></script> 
		{js_includes}
		<script type="text/javascript">
		function delete_link() {
				var del = confirm(\'Wollen Sie den Datensatz wirklich löschen?\');
				if(del == false) {
					return false;
				}
				return true;
			}
		</script>
		</head>
		
		<body>
		<div id="header">
			<div id="logo"></div>
		</div>
		<div id="space" style="height:20px;"></div>
		<div align="center">
			<div id="container">';
		$this->panel['left'] = '';
		$this->panel['right'] = '';
		return true;
	}
	
	function add_js($code, $AddJsTags = false) {
		if($AddJsTags) {
			$this->js_include .= '<script type="text/javascript">';
		}
		$this->js_include .= $code;
		if($AddJsTags) {
			$this->js_include .= '</script>';
		}
	}
	
	function add_js_file($path) {
		$this->js_files[] = $path;
	}
	
	function navbar($links) {
		$this->output .= 
		'<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10" height="26" background="../images/admin/ol.gif"></td>
					<td height="18" background="../images/admin/otop.gif" valign="middle" class="navi_box_titel">';
		
		$i = 1;
		$z = count($links);
		foreach ($links as $href => $name) {
			$this->output .= '<a href="'.$href.'">'.$name.'</a>';
			if($i != $z) {
				$this->output .= ' <font class="navi_box_titel_spacer">|</font>';
			}
			$i++;
		}
		
		$this->output .=		
					'</td>
					<td width="10" height="26" background="../images/admin/or.gif"></td>
				</tr>
				<tr>
					<td width="10" height="11" background="../images/admin/ul.gif"></td>
					<td height="11" background="../images/admin/unten.gif"></td>
					<td width="10" height="11" background="../images/admin/ur.gif"></td>
				</tr>
			</table><br />';
					
		return true;
	}
	
	function box($title, $content, $panel = 'right') {
		$this->panel[$panel] .= 
		'<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10" height="26" background="../images/admin/ol.gif"></td>
					<td height="26" background="../images/admin/otop.gif" valign="middle" class="box_titel">'.$title.'</td>
					<td width="10" height="26" background="../images/admin/or.gif"></td>
				</tr>
				<tr>
					<td width="10" background="../images/admin/left.gif"></td>
					<td bgcolor="#C5D5EA" style="border:1px solid #A3BAE9">
						<table width="100%" style="border:1px solid #DFE8F6;" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="box_text" align="left">
									'.$content.'
								</td>
							</tr>
						</table>
					</td>
					<td width="10" background="../images/admin/right.gif"></td>
				</tr>
				<tr>
					<td width="10" height="11" background="../images/admin/ul.gif"></td>
					<td height="11" background="../images/admin/unten.gif"></td>
					<td width="10" height="11" background="../images/admin/ur.gif"></td>
				</tr>
			</table><br /><br />';
	}
	
	function add($code, $panel = 'right') {
		$this->panel[$panel] .= $code;
	}
	
	function editor_js($editor = 'ckeditor') {
		// tinymce
		if($editor == 'tinymce') {
			// JS Includes
			$this->js_include = 
			'<script type="text/javascript" src="../plugins/editors/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript">
			// O2k7 skin
			tinyMCE.init({
				// General options
				mode : "exact",
				elements : "editor1",
				theme : "advanced",
				skin : "o2k7",
				plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",
		
				// Theme options
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
		
				// Example content CSS (should be your site CSS)
				content_css : "../plugins/editors/tinymce/examples/css/content.css",
		
				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "../plugins/editors/tinymce/examples/lists/template_list.js",
				external_link_list_url : "../plugins/editors/tinymce/examples/lists/link_list.js",
				external_image_list_url : "../plugins/editors/tinymce/examples/lists/image_list.js",
				media_external_list_url : "../plugins/editors/tinymce/examples/lists/media_list.js",
		
				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
			});
			</script>';
		}
		
		
		// openwysiwyg
		elseif($editor == 'openwysiwyg') {
			// JS Includes
			$this->js_include = 
			'<script language="JavaScript" type="text/javascript" src="../plugins/editors/openwysiwyg/scripts/wysiwyg.js"></script>
			<script language="JavaScript" type="text/javascript" src="../plugins/editors/openwysiwyg/scripts/wysiwyg-settings.js"></script>
			<script type="text/javascript">
			var mysettings = new WYSIWYG.Settings();
			mysettings.ImagesDir = "../plugins/editors/openwysiwyg/images/";
			mysettings.PopupsDir = "../plugins/editors/openwysiwyg/popups/"; 
			mysettings.CSSFile = "../plugins/editors/openwysiwyg/styles/wysiwyg.css"; 
			WYSIWYG.attach(\'editor1\', mysettings);
			</script>';
		}
		
		// ace
		elseif($editor == 'ace') {
			$this->end .=
			"<script>
			function inject() {
			    var baseUrl = \"../plugins/editors/ace/src/\";
			    function load(path, module, callback) {
			        path = baseUrl + path;
			        if (!load.scripts[path]) {
			            load.scripts[path] = {
			                loaded: false,
			                callbacks: [ callback ]
			            };
			
			            var head = document.getElementsByTagName('head')[0];
			            var s = document.createElement('script');
			
			            function c() {
			                if (window.__ace_shadowed__ && window.__ace_shadowed__.define.modules[module]) {
			                    load.scripts[path].loaded = true;
			                    load.scripts[path].callbacks.forEach(function(callback) {
			                        callback();
			                    });
			                } else {
			                    setTimeout(c, 50);
			                }
			            };
			            s.src = path;
			            head.appendChild(s);
			
			            c();
			        } else if (load.scripts[path].loaded) {
			            callback();
			        } else {
			            load.scripts[path].callbacks.push(callback);
			        }
			    };
			
			    load.scripts = {};
			    window.__ace_shadowed_load__ = load;
			
			    load('ace.js', 'text!ace/css/editor.css', function() {
			        var ace = window.__ace_shadowed__;
			        ace.options.mode = \"php\";
			        var Event = ace.require('pilot/event');
			        var areas = document.getElementsByTagName(\"textarea\");
			        for (var i = 0; i < areas.length; i++) {
			            Event.addListener(areas[i], \"click\", function(e) {
			                if (e.detail == 3) {
			                    ace.transformTextarea(e.target);
			                }
			            });
			        }
			    });
			}
			
			// Call the inject function to load the ace files.
			inject();
			
			var textAce;
			function initAce() {
			    var ace = window.__ace_shadowed__;
			    // Check if the ace.js file was loaded already, otherwise check back later.
			    if (ace && ace.transformTextarea) {
			        var t = document.querySelector(\"textarea\");
			        textAce = ace.transformTextarea(t);
			        textAce.setDisplaySettings(true);
			    } else {
			        setTimeout(initAce, 100);
			    }
			}
			
			// Transform the textarea on the page into an ace editor.
			initAce();
			
			document.getElementById(\"buBuild\").onclick = function() {
			    var injectSrc = inject.toString().split(\"\").join(\"\");
			    injectSrc = injectSrc.replace('baseUrl = \"src/\"', 'baseUrl=\"' + document.getElementById(\"srcURL\").value + '\"');
			
			    var aceOptions = textAce.getOptions();
			    var opt = [];
			    for (var option in aceOptions) {
			        opt.push(option + \":'\" + aceOptions[option] + \"'\");
			    }
			    injectSrc = injectSrc.replace('ace.options.mode = \"javascript\"', 'ace.options = { ' + opt.join(\",\") + ' }');
			    injectSrc = injectSrc.replace(/\s+/g, \" \");
			
			    var a = document.querySelector(\"a\");
			    a.href = \"javascript:(\" + injectSrc + \")()\";
			    a.innerHTML = \"Ace Bookmarklet Link\";
			}
			
			</script>";
		}
		
		// textarea
		elseif($editor == 'textarea') {
		}
		
		// ckeditor
		//if($editor == 'ckeditor') {
		else {
			// JS Includes
			$this->js_include = 
			'<script type="text/javascript" src="../plugins/editors/ckeditor/ckeditor.js"></script>
			<script src="../plugins/editors/ckeditor/_samles/sample.js" type="text/javascript"></script>
			<link href="../plugins/editors/ckeditor/_samles/sample.css" rel="stylesheet" type="text/css" />';
			
			// JS Code
			$this->end .= 
			"<script type=\"text/javascript\">
			//<![CDATA[
			CKEDITOR.replace( 'editor1',
				{
					/*
					 * Style sheet for the contents
					 */
					contentsCss : '../plugins/editors/ckeditor/_samples/assets/output_xhtml.css',
			
					/*
					 * Core styles.
					 */
					coreStyles_bold	: { element : 'span', attributes : {'class': 'Bold'} },
					coreStyles_italic	: { element : 'span', attributes : {'class': 'Italic'}},
					coreStyles_underline	: { element : 'span', attributes : {'class': 'Underline'}},
					coreStyles_strike	: { element : 'span', attributes : {'class': 'StrikeThrough'}, overrides : 'strike' },
			
					coreStyles_subscript : { element : 'span', attributes : {'class': 'Subscript'}, overrides : 'sub' },
					coreStyles_superscript : { element : 'span', attributes : {'class': 'Superscript'}, overrides : 'sup' },
			
					/*
					 * Font face
					 */
					// List of fonts available in the toolbar combo. Each font definition is
					// separated by a semi-colon (;). We are using class names here, so each font
					// is defined by {Combo Label}/{Class Name}.
					font_names : 'Comic Sans MS/FontComic;Courier New/FontCourier;Times New Roman/FontTimes',
			
					// Define the way font elements will be applied to the document. The \"span\"
					// element will be used. When a font is selected, the font name defined in the
					// above list is passed to this definition with the name \"Font\", being it
					// injected in the \"class\" attribute.
					// We must also instruct the editor to replace span elements that are used to
					// set the font (Overrides).
					font_style :
					{
							element		: 'span',
							attributes		: { 'class' : '#(family)' },
							overrides	: [ { element : 'span', attributes : { 'class' : /^Font(?:Comic|Courier|Times)$/ } } ]
					},
			
					/*
					 * Font sizes.
					 */
					fontSize_sizes : 'Smaller/FontSmaller;Larger/FontLarger;8pt/FontSmall;14pt/FontBig;Double Size/FontDouble',
					fontSize_style :
						{
							element		: 'span',
							attributes	: { 'class' : '#(size)' },
							overrides	: [ { element : 'span', attributes : { 'class' : /^Font(?:Smaller|Larger|Small|Big|Double)$/ } } ]
						} ,
			
					/*
					 * Font colors.
					 */
					colorButton_enableMore : false,
			
					colorButton_colors : 'FontColor1/FF9900,FontColor2/0066CC,FontColor3/F00',
					colorButton_foreStyle :
						{
							element : 'span',
							attributes : { 'class' : '#(color)' },
							overrides	: [ { element : 'span', attributes : { 'class' : /^FontColor(?:1|2|3)$/ } } ]
						},
			
					colorButton_backStyle :
						{
							element : 'span',
							attributes : { 'class' : '#(color)BG' },
							overrides	: [ { element : 'span', attributes : { 'class' : /^FontColor(?:1|2|3)BG$/ } } ]
						},
			
					/*
					 * Indentation.
					 */
					indentClasses : ['Indent1', 'Indent2', 'Indent3'],
			
					/*
					 * Paragraph justification.
					 */
					justifyClasses : [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull' ],
			
					/*
					 * Styles combo.
					 */
					stylesSet :
							[
								{ name : 'Strong Emphasis', element : 'strong' },
								{ name : 'Emphasis', element : 'em' },
			
								{ name : 'Computer Code', element : 'code' },
								{ name : 'Keyboard Phrase', element : 'kbd' },
								{ name : 'Sample Text', element : 'samp' },
								{ name : 'Variable', element : 'var' },
			
								{ name : 'Deleted Text', element : 'del' },
								{ name : 'Inserted Text', element : 'ins' },
			
								{ name : 'Cited Work', element : 'cite' },
								{ name : 'Inline Quotation', element : 'q' }
							]
			
				});
			//]]>
			</script>";
		}
	}
	
	function get() {
		$this->output .= '<div id="leftpan">'.$this->panel['left'].'</div>';
		$this->output .= '<div id="rightpan">'.$this->panel['right'].'</div>';
		$this->output .=
		'</div>
		</div>
		<div id="footer">&copy; smallCMS powered by cogax.ch</div>'.$this->end.'
		</body>
		</html>';
		return str_replace('{js_includes}', $this->js_include, $this->output);
	}
}
?>