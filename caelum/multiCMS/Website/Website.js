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
var Website = {
	elems: null,
	
	init:function(){
		Website.elems = Array();
	},
	
	add: function(sort){
		Website.elems[Website.elems.length] = sort;
	},
	
	start: function(){
		for(var i = 0; i < Website.elems.length; i++)
			Sortable.create(Website.elems[i], {
				handle:'navigationHandler_'+Website.elems[i],
				constraint: "vertical",
				onUpdate: function() {
					contentManager.rmePCR("mWebsite","","SaveNavOrder", Website.serialize()," ");
				}
			});
		
	},
	
	serialize: function(){
		cerial = "";
		for(var i = 0; i < Website.elems.length; i++)
			cerial += (cerial != "" ? ";-;;newline;;-;" : "")+Sortable.serialize(Website.elems[i]);
		
		cerial = cerial.replace(/&/g,";-;;und;;-;");
		
		return cerial;
	},

	reset: function(){
		$('linkURLEditL').parentNode.style.display = 'none';
		$('SeiteIDEditL').parentNode.style.display = 'none';
		$('inactiveTemplateIDEditL').parentNode.style.display = 'none';
		$('activeTemplateIDEditL').parentNode.style.display = 'none';
	},
	
	set: function(select){
		Website.reset();
		
		if(select.value=='cmsPage') {
			$('SeiteIDEditL').parentNode.style.display = '';
			$('inactiveTemplateIDEditL').parentNode.style.display = '';
			$('activeTemplateIDEditL').parentNode.style.display = '';
		} else if(select.value=='url') {
			$('linkURLEditL').parentNode.style.display = '';
			$('inactiveTemplateIDEditL').parentNode.style.display = '';
		} else if(select.value=='HTML') {
			$('activeTemplateIDEditL').parentNode.style.display = '';
		
		} else if(select.value=='separator') {
		
		}
	}
}
