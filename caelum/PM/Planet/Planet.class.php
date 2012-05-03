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
class Planet extends PersistentObject {
	public static function newPlanet($StarsystemID, $Name, $RX, $RY, $CourseRotation, $CourseDuration){
		$P = new Planet(-1);

		$A = $P->newAttributes();

		$A->PlanetStarsystemID = $StarsystemID;
		$A->PlanetName = $Name;
		$A->PlanetRX = $RX;
		$A->PlanetRY = $RY;
		$A->PlanetCourseRotation = $CourseRotation;
		$A->PlanetCourseDuration = $CourseDuration;

		$P->setA($A);
		$P->newMe();
	}
}
?>
