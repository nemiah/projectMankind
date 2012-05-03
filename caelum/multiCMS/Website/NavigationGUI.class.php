<?php
/*
 *  This file is part of multiCMS.

 *  multiCMS is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.

 *  multiCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  2007 - 2012, Rainer Furtmeier - Rainer@Furtmeier.de
 */
class NavigationGUI extends Navigation implements iGUIHTML2 {
	function getHTML($id){
		$U = new mUserdata();
		$U = $U->getUDValue("selectedDomain");
		
		$this->loadMeOrEmpty();
		
		if($id == -1 AND $U != null) {
			$this->A->DomainID = $U;
			$this->A->sort = -1;
			$this->A->SeiteID = -1;
		}
		
		$gui = new HTMLGUI();
		$gui->setObject($this);
		$gui->setName("Navigation");

		
		$N = new mNavigation();
		$N->addAssocV3("NavigationID","!=", $this->ID);
		$N->addAssocV3("DomainID","=",$U);
		
		//$gui->selectWithCollection("DomainID", new Domains(), "url");
		$gui->selectWithCollection("parentID", $N, "name", "kein übergeordnetes Element");

		#$gui->setType("parentID","hidden");
		#$gui->selectWithCollection("SeiteID", $aC, "name");
		
		/*$aC = new anyC();
		$aC->setCollectionOf("Seite");
		$aC->setFieldsV3(array("IF(name = '', header, name) AS name"));
		$aC->addAssocV3("DomainID","=",$this->A->DomainID);
		
		$pages = array();
		while($s = $aC->getNextEntry()){
			$pages[$s->getID()] = $s->A("name");
		}

		$gui->setType("SeiteID","select");
		$gui->setOptions("SeiteID", array_keys($pages), array_values($pages));*/
		
		$gui->setParser("SeiteID","NavigationGUI::SeiteParser",array($this->A->DomainID));
		
		$gui->setType("DomainID","hidden");
		$gui->insertSpaceAbove("activeTemplateID");
		$gui->insertSpaceAbove("linkType");
		$gui->insertSpaceAbove("hidden","Optionen", true);
		
		$gui->setType("sort","hidden");
		
		$gui->setLabel("httpsLink","Https-Link?");
		$gui->setType("httpsLink","checkbox");
		
		$gui->setLabel("activeTemplateID","Link aktiv");
		$gui->setLabel("inactiveTemplateID","Link inaktiv");
		
		$gui->setLabel("DomainID","Domain");
		$gui->setLabel("SeiteID","Seite");
		$gui->setLabel("parentID","Vaterelement");
		
		/*if($this->A->linkType == "cmsPage" OR $id == -1)
			$gui->setLineStyle("linkURL","display:none;");
		else*/
		
		$gui->setLineStyle("SeiteID","display:none;");
		$gui->setLineStyle("linkURL","display:none;");
		$gui->setLineStyle("inactiveTemplateID","display:none;");
		$gui->setLineStyle("activeTemplateID","display:none;");
		
		if($this->A->linkType == "cmsPage" OR $id == -1) {
			$gui->setLineStyle("SeiteID","");
			$gui->setLineStyle("inactiveTemplateID","");
			$gui->setLineStyle("activeTemplateID","");
		} else if($this->A->linkType == 'url') {
			$gui->setLineStyle("linkURL","");
			$gui->setLineStyle("inactiveTemplateID","");
		} else if($this->A->linkType == 'HTML') {
			$gui->setLineStyle("activeTemplateID","");
		
		} else if($this->A->linkType == 'separator') {
		
		}

		if(Session::isPluginLoaded("mMultiLanguage"))
			$gui->activateFeature("addAnotherLanguageButton", $this, "name");


		$gui->setLabel("linkType","Link-Typ");
		$gui->setInputJSEvent("linkType", "onchange","Website.set(this)");
		$gui->setLabel("linkURL","Link-URL");
		
		$T = new TemplatesGUI();
		$T->addAssocV3("templateType","=","naviTemplate");
		$gui->selectWithCollection("activeTemplateID", $T, "name");
		
		$T = new TemplatesGUI();
		$T->addAssocV3("templateType","=","naviTemplate");
		$gui->selectWithCollection("inactiveTemplateID", $T, "name");
		
		$gui->setLabel("hidden","versteckt");
		$gui->setFieldDescription("hidden","Der Menüpunkt wird auf der Seite nicht angezeigt");
		$gui->setType("hidden","checkbox");
		
		#$gui->insertSpaceAbove("parentID");
		$gui->setLabel("displaySub","Unterkat. immer anzeigen");
		$gui->setFieldDescription("displaySub","Blendet die Unterkategorien immer ein, auch wenn der Menüpunkt nicht ausgewählt ist.");
		$gui->setType("displaySub","checkbox");
		#$gui->setType("displaySub","hidden");
		$gui->setFieldDescription("httpsLink","Erzeugt einen https://...-Link");
		
		$gui->setType("linkType", "select");
		#$gui->setOptions("linkType", array("cmsPage", "url", "separator"), array("multiCMS-Seite", "URL", "Trennlinie"));
		$gui->setOptions("linkType", array("cmsPage", "url", "separator","HTML"), array("multiCMS-Seite", "URL", "Trennlinie","Template-HTML"));
		
		
		if($id == -1) $gui->setJSEvent("onSave","function() { $('contentLeft').update(); contentManager.reloadFrameRight(); }");
		else $gui->setJSEvent("onSave","function() { contentManager.reloadFrameRight(); }");
		
		$gui->setStandardSaveButton($this);
		#$gui->setSaveButtonValues(get_parent_class($this),$this->ID,"mNavigation");

		return $gui->getEditHTML();
	}
	
	public static function SeiteParser($w, $l, $p){
		$Seite = new Seite($w);
		$Seite->loadMe();
		
		$aC = new anyC();
		$aC->setCollectionOf("Seite");
		$aC->setFieldsV3(array("IF(name = '', header, name) AS name"));
		$aC->addAssocV3("DomainID","=",$p);
		
		$select = "
		<ul style=\"list-style-image:none;list-style-type:none;\">";
		
		#$select .= NavigationGUI::getOption(-1, "Neue Seite erstellen", $w, "./images/i2/new.gif");
		$select .= NavigationGUI::getOption(0, "Keine Seite", $w, "./images/i2/stop.png","margin-bottom:5px;");
		
		while($s = $aC->getNextEntry())
			$select .= NavigationGUI::getOption($s->getID(), $s->A("name"), $w);
		
		$label = $Seite->A("name") == "" ? $Seite->A("header") : $Seite->A("name");
		if($Seite->getA() == null) $label = "Seite unbekannt";
		#if($w == -1) $label = "Neue Seite erstellen";
		if($w == 0) $label = "Keine Seite";
			
		$select .= "
		</ul>";
		
		$html = "
		<input type=\"hidden\" value=\"$w\" name=\"SeiteID\" />
		
		<div onclick=\"if($('pageSelection').style.display == 'none') new Effect.BlindDown('pageSelection', { duration: 0.3 }); else new Effect.BlindUp('pageSelection', { duration: 0.3 });\"
			style=\"background-image:url(./images/i2/go-down.png);background-repeat:no-repeat;background-position:99% 2px;width:246px;padding:3px;border-bottom-style:dotted;border-bottom-width:1px;\" class=\"borderColor1 backgroundColor0\">
			<span id=\"selectedPage\">$label</span>
		</div>
		<div id=\"pageSelection\" class=\"backgroundColor0 borderColor1\" style=\"border-width:1px;border-style:solid;border-top-width:0px;position:absolute;display:none;width:250px;\">
			<div style=\"overflow:auto;height:150px;\">
			$select
			</div>
		</div>";
		
		return $html;
	}
	
	private static function getOption($value, $label, $preset, $backgroundImage = "", $style = ""){
		return "
			<li
				onclick=\"
					$('selectedPage').update('$label');
					new Effect.BlindUp('pageSelection', { duration: 0.3 });
					
					if($('SeiteIDValues'+$('AjaxForm').SeiteID.value))
						$('SeiteIDValues'+$('AjaxForm').SeiteID.value).style.fontWeight = 'normal';
					
					this.style.fontWeight = 'bold';
					$('AjaxForm').SeiteID.value = '$value';\"
				style=\"padding:3px;margin:0px;cursor:pointer;background-position:99% 2px;background-repeat:no-repeat;".($backgroundImage != "" ? "background-image:url(".$backgroundImage.");" : "").($preset == $value ? "font-weight:bold;" : "")."$style\"
				onmouseover=\"this.className = 'backgroundColor2';\"
				onmouseout=\"this.className = '';\"
				id=\"SeiteIDValues".$value."\">$value - $label</li>";
	}
}
?>