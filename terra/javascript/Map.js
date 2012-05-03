var Map = {
	mode: null,

	enterEditMode: function(mode){
		switch (mode) {
			case "starsystem":
				Map.mode = mode;
				Event.observe("playField","click", Map.work);
			break;
		}

	},

	leaveEditMode: function(mode){
		switch (mode) {
			case "starsystem":
				Map.mode = null;
				Event.stopObserving("playField", "click", Map.work);
			break;
		}
	},

	work: function(event){
		switch (Map.mode) {
			case "starsystem":
				var pos = Playfield.calcMousePosition(event);
				Interface.newAjaxSet("JSON", "StarsystemCreator", {"posX":pos[0], "posY":pos[1]}, function(transport) { Playfield.parseJSON(transport.responseText, "map") });
			break;

			case "zoomToTarget":
				//var posi = Playfield.calcMousePosition(event);
				posi = event.target.getAttribute("transform").match(/([0-9\-]*), ([0-9\-]*)/g)[0].split(", ");

				Map.leaveZoomToTargetMode();
				Playfield.toggleMap();
				Playfield.translateX = (- parseInt(posi[0])) * Playfield.scale + document.viewport.getDimensions().width/2;
				Playfield.translateY = (- parseInt(posi[1]))*0.6 * Playfield.scale + document.viewport.getDimensions().height/2;

				 Playfield.rotateAtX = parseInt(posi[0]);
				 Playfield.rotateAtY = parseInt(posi[1]);

				Playfield.update();
			break;
		}
	},

	enterZoomToTargetMode: function(){
		Map.mode = "zoomToTarget";
		
		$('map').style.cursor = 'crosshair';
		Event.observe("map","click", Map.work);
	},

	leaveZoomToTargetMode: function(){
		Map.mode = null;

		$('map').style.cursor = 'auto';
		Event.stopObserving("map", "click", Map.work);
		Interface.unsetButton("mapControls");
	}
	
}