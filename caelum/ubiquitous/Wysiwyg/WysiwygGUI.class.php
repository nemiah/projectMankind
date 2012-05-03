<?php
/*
 *  This file is part of ubiquitous.

 *  ubiquitous is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.

 *  ubiquitous is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  2007 - 2012, Rainer Furtmeier - Rainer@Furtmeier.de
 */
#				theme_advanced_buttons3_add : "save,tablecontrols",

class WysiwygGUI extends UnpersistentClass {
	public function getEditor(){
		
		$bps = $this->getMyBPSData();
		
		$content = new $bps["FieldClass"]($bps["FieldClassID"]);
		$content->loadMe();
		
		$buttons = '
				theme_advanced_buttons1 : "save,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,charmap,|,insertdate,inserttime,|,justifyleft,justifycenter,justifyright,justifyfull",
				theme_advanced_buttons2 : "bold,italic,underline,strikethrough,styleprops,|,formatselect,|,link,unlink,anchor,image,media,pagebreak,code,|,forecolor,backcolor,|,preview",
				theme_advanced_buttons3 : "bullist,numlist,|,outdent,indent,blockquote,|,insertlayer,moveforward,movebackward,absolute,|,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",';
		
		if($bps["FieldClass"] == "Dokument") $buttons = '
				theme_advanced_buttons1 : "save,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,charmap,|,insertdate,inserttime,removeformat,code",
				theme_advanced_buttons2 : "bold,italic,underline,|,formatselect,|,fontselect,fontsizeselect,forecolor",
				theme_advanced_buttons3 : null,';
		
		
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>tinyMCE</title>
		
		<!--<script type="text/javascript" src="../libraries/scriptaculous/prototype.js"></script>
		<script type="text/javascript" src="../libraries/scriptaculous/effects.js"></script>-->
		
		<script type="text/javascript" src="../libraries/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="../libraries/jquery/jquery-ui-1.8.17.custom.min.js"></script>
		<script type="text/javascript" src="../javascript/P2J.js"></script>
		<script type="text/javascript" src="../javascript/handler.js"></script>
		<script type="text/javascript" src="../javascript/contentManager.js"></script>
		<script type="text/javascript" src="../javascript/Interface.js"></script>
		<script type="text/javascript" src="../javascript/Overlay.js"></script>
		<script type="text/javascript" src="../libraries/webtoolkit.base64.js"></script>
		<script type="text/javascript" src="../ubiquitous/Wysiwyg/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="../ubiquitous/Wysiwyg/tiny_mce/jquery.tinymce.js"></script>
		
		<script type="text/javascript">

			tinyMCE.init({
				mode : "textareas",
				theme : "advanced",'.$buttons.'
				'.($bps["FieldClass"] == "Dokument" ? 'content_css : "../ubiquitous/Wysiwyg/office.css",' : 'content_css : "../ubiquitous/Wysiwyg/content.css",').'
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				plugins: "save,table,spellchecker,searchreplace,insertdatetime,paste,inlinepopups,pagebreak,preview,media,layer,advimage,style,safari,tabfocus",
				save_onsavecallback : "ajaxSave",
				language: "de",
				plugin_preview_height : "550",
				plugin_preview_width : "750",
				convert_urls : false'.($bps["FieldClass"] == "Dokument" ? ',
				theme_advanced_font_sizes: "8pt,9pt,10pt,12pt,14pt,18pt,24px,36pt",
				font_size_style_values : "8pt,9pt,10pt,12pt,14pt,18pt,24px,36pt",
				theme_advanced_fonts: "Helvetica=helvetica;Courier=courier;Times New Roman=times new roman;Ubuntu=Ubuntu;Orbitron=Orbitron;Raleway=Raleway"' : '').'
				
			});
			
			function ajaxSave(content) {
				$(\''.$bps["FieldName"].'\').value = Base64.encode(content.getContent());

				saveClass(
					\''.$content->getClearClass().'\',
					\''.$content->getID().'\',
					function() {
						if("'.$bps["FieldClass"].'" == "Content")
							opener.contentManager.updateLine("", '.$bps["FieldClassID"].', "mContent");
					},
					"HTMLEditorForm");
			}
			contentManager.setRoot("'.str_replace("interface".DIRECTORY_SEPARATOR."rme.php", "", $_SERVER["SCRIPT_NAME"]).'");
			contentManager.startAutoLogoutInhibitor();
		</script>
		
		<link rel="stylesheet" type="text/css" href="../styles/'.(isset($_COOKIE["phynx_color"])? $_COOKIE["phynx_color"] : "standard").'/colors.css"></link>
		<link rel="stylesheet" type="text/css" href="../styles/standard/general.css"></link>

		<style type="text/css">
			* {
				margin:0px;
				padding:0px;
			}
		</style>
	</head>
	<body>
		<form id="HTMLEditorForm">
			<textarea id="'.$bps["FieldName"].'" name="'.$bps["FieldName"].'" style="width:100%;height:630px;">
				'.(base64_decode($content->A($bps["FieldName"]))).'
			</textarea>
				
		</form>
		<div id="messenger" style="left:-210px;top:0px;" class="backgroundColor3 borderColor1"></div>
	
	</body>
	<script type="text/javascript">
		Event.observe(window, "resize",function() {
			//var XY = document.viewport.getDimensions();

			$(\'text_ifr\').style.height = ($j(window).height() - $j(\'#text_toolbargroup\').height() - 10)+"px";
		});

	</script>
</html>';

		echo $html;
		
	}
	
	public function editInPopup($formID, $fieldName, $variablesCallback = null){

		$ITA = new HTMLInput("tinyMCEEditor", "textarea");
		$ITA->id("tinyMCEEditor");
		$ITA->style("width:100%;height:500px;");
		
		if($variablesCallback != null)
			echo "<div style=\"float:right;width:190px;margin:5px;\">
					<p><small>Sie können folgende Variablen in Ihrem Text verwenden (bitte beachen Sie Groß- und Kleinschreibung):</small></p>
					<p style=\"margin-top:5px;\" id=\"tinyMCEVars\"></p></div>";
		echo "<div style=\"width:".($variablesCallback != null ? "800" : "1000")."px;\">".$ITA."</div>";
		
		#$rand = rand(100, 13454832910);
		
		echo '
			<style type="text/css">
				#tinyMCEEditor_toolbargroup table {
					width:auto;
					margin:0px;
				}
			</style>
			<script type="text/javascript">
				tinymce.create("tinymce.plugins.ExamplePlugin", {
					createControl: function(n, cm) {
						switch (n) {
							case "mylistbox":
								var mlb = cm.createListBox("mylistbox", {
									 title : "Variablen",
									 onselect : function(v) {
										 tinyMCE.activeEditor.windowManager.alert("Value selected:" + v);
									 }
								});

								// Add some values to the list box
								mlb.add("Some item 1", "val1");
								mlb.add("some item 2", "val2");
								mlb.add("some item 3", "val3");
								mlb.add("some item 4", "val4");

								// Return the new listbox instance
								return mlb;
						}

						return null;
					}
				});
				
				tinymce.PluginManager.add("variablen", tinymce.plugins.ExamplePlugin);

				if(!$("tinyMCEEditor").value.match("</p>"))
					$("tinyMCEEditor").value = $("'.$formID.'").'.$fieldName.'.value.replace(/\n/g, "<br />");
				else
					$("tinyMCEEditor").value = $("'.$formID.'").'.$fieldName.'.value;
				$j("#tinyMCEEditor").tinymce({
				mode : "textareas",
				theme : "advanced",
					plugins: "-variablen,save,table,spellchecker,searchreplace,insertdatetime,paste,inlinepopups,pagebreak,preview,media,layer,advimage,style,safari,tabfocus",
					theme_advanced_buttons1 : "save,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,charmap,|,insertdate,inserttime,removeformat,code",
					theme_advanced_buttons2 : "mylistbox,|,bold,italic,underline,|,forecolor",
					theme_advanced_buttons3 : null,
					save_onsavecallback : function(content){ $("'.$formID.'").'.$fieldName.'.value = content.getContent(); },
					theme_advanced_toolbar_location : "top"/*,
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false*/
				});
				'.($variablesCallback != null ? "$variablesCallback();" : "").'
			</script>';
	}
}
?>