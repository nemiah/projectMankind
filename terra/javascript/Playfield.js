var Playfield = {
    scale: 1.5,
	scaleMap: 1,
	defaultScaleMap: 0.002,

    translateX: 0,
    translateY: 0,

    translateMapX: 0,
    translateMapY: 0,
	
    lastMousePosX: 0,
    lastMousePosY: 0,

	rotateAtX: 0,
	rotateAtY: 0,

	NS: "http://www.w3.org/2000/svg",
	NSXLink: "http://www.w3.org/1999/xlink",

	displayMode: "game",

    init: function(){

		Playfield.resizeToViewPort();

		Event.observe(document, "mousewheel", Playfield.scaleMe, false);
		Event.observe(document, "DOMMouseScroll", Playfield.scaleMe, false); // Firefox
		
		Event.observe(window, "resize", Playfield.resizeToViewPort);
		//Event.observe(window, "resize", Playfield.recenterMap);

		var svg = document.createElementNS(Playfield.NS, "svg");
		svg.setAttributeNS(null, "version", "1.1");
		svg.setAttributeNS(null, "preserveAspectRatio", "xMidYMid slice");
		svg.setAttributeNS(Playfield.NS, "xlink", Playfield.NSXLink);

		var interf = document.createElementNS(Playfield.NS, "g");
		interf.setAttributeNS(null, "id", "interface");

		var data = document.createElementNS(Playfield.NS, "g");
		data.setAttributeNS(null, "id", "data");

		var background = document.createElementNS(Playfield.NS, "g");
		background.setAttributeNS(null, "id", "background");

		var map = document.createElementNS(Playfield.NS, "g");
		map.setAttributeNS(null, "id", "map");
		map.setAttributeNS(null, "transform", "translate(0 0) scale("+Playfield.defaultScaleMap+")");

		var grid = document.createElementNS(Playfield.NS, "g");
		grid.setAttributeNS(null, "id", "grid");
		grid.setAttributeNS(null, "transform", "scale ("+Playfield.scale+") scale(1 0.6) rotate(10 "+Playfield.rotateAtX+" "+Playfield.rotateAtY+")");

		var universe = document.createElementNS(Playfield.NS, "g");
		universe.setAttributeNS(null, "id", "universe");
		universe.setAttributeNS(null, "transform", "scale ("+Playfield.scale+") scale(1 0.6) rotate(10 "+Playfield.rotateAtX+" "+Playfield.rotateAtY+")");

		svg.appendChild(data);
		svg.appendChild(background);
		svg.appendChild(grid);
		svg.appendChild(universe);
		svg.appendChild(map);
		svg.appendChild(interf);
		$('playField').appendChild(svg);
		//Playfield.recenterMap();

		Event.observe($('playField'), "mousemove", Playfield.calcMousePosition);
		Event.observe($('playField'), "mousedown", Playfield.scrollStart);
		Event.observe($('playField'), "mouseup", Playfield.scrollEnd);

		Event.observe($("iconAdmin"), "click", function() { window.open(system.caelumURL); });

		/*var img = new Image();
		img.src = system.dataURL+"/images/icons_32x32/map.png";
		img.onload = function(){
			var ctx = $('iconMap').getContext('2d');
			$('iconMap').style.width = "32px";
			$('iconMap').style.height = "32px";
			ctx.drawImage(img,0,0);
		}*/

		Event.observe($("iconMap"), "click", Playfield.toggleMap);
		Event.observe($("iconMessages"), "click", function(){ new Window("Messages","SysMessages",{}, {"style":"width:500px;"});});

		Interface.newAjaxGet("SVG", "Data", {}, function(transport){ Playfield.parseJSON(transport.responseText, "data"); });
		Interface.newAjaxGet("SVG", "Background", {}, function(transport){ Playfield.parseJSON(transport.responseText, "background"); });
		Interface.newAjaxGet("SVG", "Init", {}, function(transport){ Playfield.parseJSON(transport.responseText, "interface"); });
		Interface.newAjaxGet("SVG", "Grid", {}, function(transport){ Playfield.parseJSON(transport.responseText, "grid"); });
		Interface.newAjaxGet("SVG", "Starsystems", {}, function(transport){ Playfield.parseJSON(transport.responseText, "universe"); });
		Interface.newAjaxGet("SVG", "Map", {}, function(transport){ Playfield.parseJSON(transport.responseText, "map"); });
    },

	toggleMap: function(){
		new Window("Map controls","MapControls","", {showClose: false, showReload: false, style :"width:150px;"});
		
		//Pixastic.process($("iconMap"), "desaturate");

		if(Playfield.displayMode == "game"){
			Playfield.recenterMap();
			$('map').style.display = "block";
			$('universe').style.display = "none";
			$('grid').style.display = "none";
			Playfield.displayMode = "map";
		} else {
			$('map').style.display = "";
			$('universe').style.display = "block";
			$('grid').style.display = "block";
			Playfield.displayMode = "game";
		}
	},

	recenterMap: function(){
		Playfield.translateMapX = document.viewport.getDimensions().width/4;
		Playfield.translateMapY = document.viewport.getDimensions().height/4;
		Playfield.updateMap();
	},

	calcMousePosition: function(event){
		var calcMousePosX = (event.clientX-Playfield.translateMapX)/Playfield.scaleMap/Playfield.defaultScaleMap;
		var calcMousePosY = (event.clientY-Playfield.translateMapY)/Playfield.scaleMap/Playfield.defaultScaleMap;
		if($('mousePos')) $('mousePos').update(Math.round(calcMousePosX)+":"+Math.round(calcMousePosY));
		return [calcMousePosX, calcMousePosY];
	},

	parseJSON: function(string, appendto){
		jsonObj = JSON.parse(string);
		
		for (var i = 0; i < jsonObj.length; i++) {
			elem = Playfield.generateSVG(jsonObj[i]);
			$(appendto).appendChild(elem);
		}
	},

	generateSVG: function(jsonObj){
		var elem = document.createElementNS(Playfield.NS, jsonObj["tag"]);

		for (var s in jsonObj) {
			if(s == "sub") continue;
			if(s == "tag") continue;
			if(s == "value") {
				elem.appendChild(document.createTextNode(jsonObj[s]));
				continue;
			}
			if(s == "eventOnclick"){
				var temp = jsonObj[s];
				Event.observe(elem, "click",function(){ eval(temp) });
				continue;
			}

			if (typeof(jsonObj[s]) == "function") continue;

			if(s == "href"){
				elem.setAttributeNS(Playfield.NSXLink, s, jsonObj[s]);
				continue;
			}

			elem.setAttributeNS(null, s, jsonObj[s]);

		}

		if(typeof jsonObj["sub"] != "undefined")
			for (s in jsonObj["sub"]){
				if (typeof(jsonObj["sub"][s]) == "function") continue;
				elem.appendChild(Playfield.generateSVG(jsonObj["sub"][s]));
			}

		return elem;
	},

    scrollStart: function(e){
		Event.observe($('playField'), "mousemove", Playfield.scroll);

		Playfield.lastMousePosX = e.clientX;
		Playfield.lastMousePosY = e.clientY;
	},

	scrollEnd: function(e){
		Event.stopObserving($('playField'), "mousemove", Playfield.scroll);
	},

	scroll: function(e){
		deltaX = Playfield.lastMousePosX - e.clientX;
		deltaY = Playfield.lastMousePosY - e.clientY;

		if(Playfield.displayMode == "game") {
			Playfield.translateX -= deltaX;
			Playfield.translateY -= deltaY;
			Playfield.update();
		}

		if(Playfield.displayMode == "map") {
			Playfield.translateMapX -= deltaX;
			Playfield.translateMapY -= deltaY;
			Playfield.updateMap();
		}

		Playfield.lastMousePosX = e.clientX;
		Playfield.lastMousePosY = e.clientY;

		/*$('starfield3').childNodes[0].setAttributeNS(null, 'transform', "translate("+Playfield.translateX/5+" "+Playfield.translateY/5+")");
		*/
		//$('starfield2').childNodes[0].setAttributeNS(null, 'transform', "translate("+Playfield.translateX/10+" "+Playfield.translateY/10+")");
		/*$('starfield1').childNodes[0].setAttributeNS(null, 'transform', "translate("+Playfield.translateX/50+" "+Playfield.translateY/50+")");
*/
		/*$('playField').style.backgroundPosition = Playfield.translateX/5+"px "+Playfield.translateY/5+"px";
		$('playFieldBG1').style.backgroundPosition = Playfield.translateX/10+"px "+Playfield.translateY/10+"px";
		$('playFieldBG2').style.backgroundPosition = Playfield.translateX/50+"px "+Playfield.translateY/50+"px";
*/
    },
	
    update: function(){
		//width = document.viewport.getDimensions().width;
		//height = document.viewport.getDimensions().height;

		$('universe').setAttributeNS(null, 'transform', " translate("+Playfield.translateX+" "+Playfield.translateY+") scale("+Playfield.scale+") scale(1, 0.6) rotate(10 "+Playfield.rotateAtX+" "+Playfield.rotateAtY+")");
    },

	updateMap: function(){
		$('map').setAttributeNS(null, 'transform', " scale("+Playfield.scaleMap+") translate("+Playfield.translateMapX / Playfield.scaleMap+" "+Playfield.translateMapY / Playfield.scaleMap+") scale("+Playfield.defaultScaleMap+")");
	},

    resizeToViewPort: function(){
		$("playField").style.height = document.viewport.getDimensions().height+"px";
    },
	
    scaleMe: function(e){
		if(Playfield.displayMode != "map") return;

		delta = Event.wheel(e) / 20;
		
		var oldScale = Playfield.scaleMap;

		if(Playfield.scaleMap + delta > 0.5 && Playfield.scaleMap + delta < 4) Playfield.scaleMap += delta;
		else return;

		var pos = Playfield.calcMousePosition(e);

		Playfield.translateMapX -= -pos[0] * oldScale * Playfield.defaultScaleMap + pos[0] * Playfield.scaleMap * Playfield.defaultScaleMap;
		Playfield.translateMapY -= -pos[1] * oldScale * Playfield.defaultScaleMap + pos[1] * Playfield.scaleMap * Playfield.defaultScaleMap;

		Playfield.updateMap();
    }

	/*center: function(e, x, y){
		Playfield.translateX = e.clientX - x * Playfield.scale;

		Playfield.translateY = e.clientY - y * Playfield.scale;

		$('svgElements').setAttributeNS(null, 'transform', " translate("+Playfield.translateX+" "+Playfield.translateY+") scale("+Playfield.scale+") scale(1, 0.6) rotate(-10)");
    },*/

}

Event.observe(window, "load", Playfield.init);

Object.extend(Event, {
    wheel:function (event){
	var delta = 0;
	if (!event) event = window.event;
	if (event.wheelDelta) {
	    delta = event.wheelDelta/120;
	    if (window.opera) delta = -delta;
	} else if (event.detail) {
	    delta = -event.detail/3;
	}
	return Math.round(delta); //Safari Round
    }
});