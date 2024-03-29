<?php
/*
 *  This file is part of phynx.

 *  phynx is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.

 *  phynx is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  2007 - 2012, Rainer Furtmeier - Rainer@Furtmeier.de
 */
class UtilGUI extends Util {
	function __construct($nonSense = ""){}
	
	static function CLDateParser($date, $l = "load"){
		echo Util::CLDateParser($date, $l);
	}

	static function CLNumberParserZ($number, $l = "load") {
		echo parent::CLNumberParserZ($number, $l);
	}

	public function reloadApplication(){
		session_destroy();
	}
	
	/**
	 * Method to display an E-Mail popup for easy E-Mail sending
	 * 
	 * Requires a method named "getEMailData" in $dataClass which returns an array:
	 * array(fromName, fromAddress, recipientName, recipientAddress, subject, body)
	 * 
	 * Will call $dataClass($dataClassID)::sendEmail afterwards
	 * 
	 * @param string $dataClass
	 * @param string $dataClassID 
	 */
	public static function EMailPopup($dataClass, $dataClassID, $callbackParameter = null, $onSuccessFunction = null){
		$c = new $dataClass($dataClassID);
		$data = $c->getEMailData($callbackParameter);

		$tab = new HTMLTable(2);
		$tab->setColWidth(1, "120px;");
		$tab->addLV("Absender:", "$data[fromName]<br /><small>&lt;$data[fromAddress]&gt;</small>");
		if(count($data["recipients"]) == 1)
			$tab->addLV("Empfänger:", $data["recipients"][0][0]."<br /><small>&lt;".$data["recipients"][0][1]."&gt;</small>");
		else {
			$recipients = array();
			foreach($data["recipients"] AS $ID => $Rec)
				$recipients[$ID] = new HTMLInput ($Rec[0]." &lt;".$Rec[1]."&gt;", "option", $ID);;
			
			$IS = new HTMLInput("EMailRecipient$dataClassID", "select", "0", $recipients);
			$IS->id("EMailRecipient$dataClassID");
			
			$tab->addLV("Empfänger:", $IS);
		}
		$tab->addLV("Betreff:", "<input type=\"text\" id=\"EMailSubject$dataClassID\" value=\"$data[subject]\" />");
		$tab->addRow(array("<textarea id=\"EMailBody$dataClassID\" style=\"width:100%;height:300px;font-size:10px;\">$data[body]</textarea>"));
		$tab->addRowColspan(1, 2);
		$tab->addRowClass("backgroundColor0");

		if($onSuccessFunction == null)
			$onSuccessFunction = "".OnEvent::reload("Left")." Popup.close('Util', 'edit');";
		

		$BAbort = new Button("Abbrechen","stop");
		$BAbort->onclick("Popup.close('Util', 'edit');");
		$BAbort->style("margin-bottom:10px;margin-top:10px;");
		
		$BGo = new Button("E-Mail\nsenden","okCatch");
		$BGo->style("float:right;margin-top:10px;");
		$BGo->rmePCR($dataClass, $dataClassID, "sendEmail", array("$('EMailSubject$dataClassID').value", "$('EMailBody$dataClassID').value", count($data["recipients"]) == 1 ? "0" : "$('EMailRecipient$dataClassID').value", "'".$callbackParameter."'"), $onSuccessFunction);
		#$BGo->onclick("CloudKunde.directMail('$this->ID', '$data[recipientAddress]', $('EMailSubject$this->ID').value, $('EMailBody$this->ID').value); ");


		$tab->addRow(array($BGo.$BAbort));
		$tab->addRowColspan(1, 2);
		#$tab->addRowClass("backgroundColor0");

		echo $tab;
	}

	public static function newSession($physion, $application, $plugin){
		echo "<p>Bitte haben Sie etwas Geduld, während die neue Sitzung initialisiert wird...</p><iframe onload=\"window.open(contentManager.getRoot()+'?physion=$physion&application=$application&plugin=$plugin');".OnEvent::closePopup("Util")."\" src=\"interface/rme.php?class=Users&construct=&method=doLogin&parameters=%27".Session::currentUser()->A("username")."%27,%27".Session::currentUser()->A("SHApassword")."%27,%27".Applications::activeApplication()."%27,%27".Session::currentUser()->A("language")."%27&physion=$physion\" style=\"display:none;\"></iframe>";
	}
}

?>