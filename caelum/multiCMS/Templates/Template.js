/*
 *
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
 var CMSTemplate = {
	updateVariables: function(select){
		if($(oldVarSelected+"Variables")) $(oldVarSelected+"Variables").style.display = "none";
		
		if($(select.value+"Variables")) {
		
			$('TBVarsContainer').style.display = "";
			$(select.value+"Variables").style.display = "";
			
			oldVarSelected = select.value;
			
		} else {
		
			$('TBVarsContainer').style.display = "none";
			oldVarSelected = null;
			
		}
	}
}