<?php
/**
 *  This file is part of PM.

 *  PM is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.

 *  PM is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  2007 - 2012, Rainer Furtmeier - Rainer@Furtmeier.de
 */
class mToolGUI extends UnpersistentClass implements iGUIHTML2 {
	public function getHTML($id){
		$gui = new HTMLGUI();
		$gui->VersionCheck("mTool");
		
		$FB = new FileBrowser();
		$FB->addDir("../PM/GoD");

		$files = $FB->getAsLabeledArray("iPMTool",".class.php",true);
		
		$tab = new HTMLTable(2, "Tools");
		$tab->setColWidth(1, "20px");
		foreach($files as $key => $value){
			$B = new Button("","./images/i2/edit.png");
			$B->type("icon");
			$B->onclick("loadFrameV2('contentLeft','$value');");
			$tab->addRow(array($B,$key));
		}
		
		return $tab;
	}
}
?>