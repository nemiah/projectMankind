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
class StarsystemGUI extends Starsystem implements iGUIHTML2 {
	function getHTML($id){
		
		$this->loadMeOrEmpty();
		
		$gui = new HTMLGUI();
		$gui->setObject($this);
		$gui->setName("Starsystem");

		$gui->setStandardSaveButton($this);

		$B = new Button("create\nplanets","./PM/Planet/Terran01.png");
		$B->rme("Starsystem", $this->ID, "createPlanets", "");
		$tab = new HTMLTable(1);
		$tab->addRow($B);

		$gui->setType("StarsystemGalaxyID", "hidden");
		$gui->setLabel("StarsystemName", "Name");
		$gui->setLabel("StarsystemX", "X");
		$gui->setLabel("StarsystemY", "Y");
		$gui->setLabel("StarsystemRotation", "Rotation");

		return $tab.$gui->getEditHTML();
	}

        public function createPlanets(){
			$numberOfPlanets = mt_rand(5,8);


			for($i = 0; $i < $numberOfPlanets; $i++) {
				$CourseRadius = pow(($i + 1),1.5) * 50;
				$CourseDist = mt_rand(2, 8)/10;

				$rx = $CourseRadius * $CourseDist;
				$ry = $CourseRadius - $rx;
				$rd = ($rx + $ry) / 2;

				#1209600

				$CourseDuration = $CourseRadius * 12096;
				#echo $CourseDist."\n";

				Planet::newPlanet($this->ID, "Planet ".($i+1), $rx, $ry, round(mt_rand(0,360)), $CourseDuration);

			}
		}
}
?>