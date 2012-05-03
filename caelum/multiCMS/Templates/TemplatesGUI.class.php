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
class TemplatesGUI extends Templates implements iGUIHTMLMP2, iCategoryFilter {
	
	function getHTML($id, $page){
		$gui = new HTMLGUI();
		$gui->VersionCheck("Templates");
		
		$U = new mUserdata();
		$U = $U->getUDValue("selectedDomain");
		
		if($U == null) {
			$t = new HTMLTable(1);
			$t->addRow("Sie haben keine Domain ausgewählt.<br /><br />Bitte wählen Sie eine Domain im Domain-Plugin, indem Sie auf das graue Kästchen in der Liste auf der rechten Seite klicken.");
			return $t->getHTML();
		}

		$this->addAssocV3("TemplateDomainID","=",$U);
		$this->addAssocV3("TemplateDomainID","=","0","OR");
		$this->addOrderV3("templateType", "ASC");
		#if($this->A == null) $this->lCV3($id);
		
		$gui->showFilteredCategoriesWarning($this->filterCategories(), $this->getClearClass());
		$gesamt = $this->loadMultiPageMode($id, $page, 0);
		$gui->setMultiPageMode($gesamt, $page, 0, "contentRight", str_replace("GUI","",get_class($this)));
		
		$gui->setName("Template");
		if($this->collector != null) $gui->setAttributes($this->collector);
		$gui->setShowAttributes(array("aktiv","name"));
		
		$gui->setParser("aktiv","TemplatesGUI::aktivParser",array("\$aid"));
		$gui->setColWidth("aktiv","20px");
		
		$gui->setDisplayGroup("templateType", $this->getAvailableCategories());
		$gui->setCollectionOf($this->collectionOf);
		
		try {
			return $gui->getBrowserHTML($id);
		} catch (Exception $e){ }
	}
	
	function getAvailableCategories(){
		return array("contentTemplate" => "Content-Template", "presetTemplate" => "HTML-Template", "pageTemplate" => "Page-Template", "domainTemplate" => "Domain-Template",/* "listTemplate" => "List-Template", "tableTemplate" => "Table-Template",*/ "dlTemplate" => "Download-Template", "naviTemplate" => "Navigation-Template");
	}
	
	function getCategoryFieldName(){
		return "templateType";
	}
	
	public function activate($id){
		$S = new Template($id);
		$S->changeA("aktiv","1");
		
		$this->addAssocV3("aktiv","=","1");
		$this->addAssocV3("templateType", "=", $S->getA()->templateType);
		while($t = $this->getNextEntry()){
			$t->changeA("aktiv","0");
			$t->saveMe();
		}
		
		$S->saveMe();
	}
	
	public static function aktivParser($w, $a, $p){
		return $w == 1 ? "<img src=\"./images/i2/ok.gif\" title=\"ist default-Template dieser Kategorie\" />" : "<img src=\"./images/i2/notok.gif\" title=\"als default-Template dieser Kategorie markieren\" onclick=\"rme('Templates','','activate','$p','contentManager.reloadFrameRight();');\" class=\"mouseoverFade\" />";
	}
	
	public static function getDefault($templateType){
		$T = new TemplatesGUI();
	
		$T->addAssocV3("aktiv","=","1");
		$T->addAssocV3("templateType", "=", $templateType);
		$t = $T->getNextEntry();
		
		return $t->getID();
	}
}
?>