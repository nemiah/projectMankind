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
class PMToolStarsystemCreatorGUI implements iPMTool, iGUIHTML2 {
	public function getLabel(){
		return "Starsystem creator";
	}

	public function getHTML($id){
		$tab = new HTMLTable(3, "Koordinaten");
		$tab->setColWidth(1, "20px");
		for($i = 0; $i < 200; $i++){

			$x = round(($i + 1) * 1000 * sin(($i + mt_rand(-0.9, 0.9)) * pi() / (5)));
			$y = round(($i + 1) * 1000 * cos(($i + mt_rand(-1.2, 1.2)) * pi() / (3)));

			$tab->addRow(array($i.": ","$x","$y"));
			$tab->addCellStyle(1, "text-align:right");
			$tab->addCellStyle(2, "text-align:right");

			$S = new Starsystem(-1);
			$SA = $S->newAttributes();
			$SA->StarsystemName = "Starsystem ".($i + 1);
			$SA->StarsystemX = $x;
			$SA->StarsystemY = $y;
			$SA->StarsystemRotation = rand(0, 180);

			$S->setA($SA);
			$S->newMe();
		}

		return $tab;
	}
}
?>