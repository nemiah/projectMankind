var WindowManager = {
	number: 0,
	topNumber: 100,

	registeredWindows: Array(),

	openWindows: new Object()
}

function Window(windowName, callClass, callParameter, optionsWindow, optionsContent){

	if(typeof optionsWindow == "undefined") optionsWindow = {};
	
	this.number = ++WindowManager.number;
	this.reload = false;

	var callMe = "";

	if(typeof callParameter == "string")
		callMe = "&parameter="+callParameter;

	if(typeof callParameter == "object")
		for(var e in callParameter) {
			if (typeof(callParameter[e]) == "function") continue;

			callMe += "&"+e+"="+callParameter[e];
		}

	if(typeof callParameter == "undefined") callParameter = {};
	this.callClass = callClass;
	this.callMe = callMe;
	this.callParameter = callParameter;
	this.optionsWindow = optionsWindow;
	this.optionsContent = optionsContent;
	this.windowName = windowName;

	if(WindowManager.openWindows[callClass+";"+callMe] != "undefined" && WindowManager.openWindows[callClass+";"+callMe] > 0){
		if(WindowManager.registeredWindows[WindowManager.openWindows[callClass+";"+callMe]]) WindowManager.registeredWindows[WindowManager.openWindows[callClass+";"+callMe]].closeMe();
		WindowManager.openWindows[callClass+";"+callMe] = 0;
		return;
	}
	else WindowManager.openWindows[callClass+";"+callMe] = this.number;
	
	this.load();
}

Window.prototype.reloadMe = function(){
	this.reload = true;
	this.load();
	this.reload = false;
}

Window.prototype.load = function(){
	number = this.number;
	me = this;
	optionsContent = this.optionsContent;
	optionsWindow = this.optionsWindow;
	windowName = this.windowName;
	isReload = this.reload;

	new Ajax.Request("./interface/getData.php", { parameters: "t=HTML;"+this.callClass+"&parameters="+JSON.stringify(me.callParameter),method:"post", onSuccess: function(transport){
		if(Interface.checkResponse(transport)){

			if(!isReload){
				var content = Builder.node("p", {id: "floatingWindowContent"+number, className:"windowContent", style: "overflow:auto;"+((typeof optionsContent != "undefined" && typeof optionsContent.style != "undefined") ? optionsContent.style : "max-height:300px;")},"");

				var handle = Builder.node("div", {id: "floatingWindowHandle"+number, className:"windowHandle"},[
					windowName,
					(typeof optionsWindow.showClose == "undefined" || optionsWindow.showClose != false) ? Builder.node("div",{className:"windowClose", onclick:"WindowManager.registeredWindows["+number+"].closeMe();"},"X") : "",
					(typeof optionsWindow.showReload == "undefined" || optionsWindow.showReload != false) ? Builder.node("div",{className:"windowReload", onclick:"WindowManager.registeredWindows["+number+"].reloadMe();"},"R") : ""
				]);

				var window = Builder.node("div", {id: "floatingWindow"+number, className:"window", style: "display:none;"+((typeof optionsWindow != "undefined" && typeof optionsWindow.style != "undefined") ? optionsWindow.style : "")}, [handle, content]);

				$('windowsContainer').appendChild(window);

				new Draggable('floatingWindow'+number, {
					handle: "floatingWindowHandle"+number,
					snap: Window.prototype.windowSnap,
					zindex: 100000
					});

				WindowManager.registeredWindows[number] = me;

				var no = number;
				Event.observe($('floatingWindow'+number), "click", function(){ WindowManager.registeredWindows[no].toTop(); });

				$('floatingWindowContent'+number).update(transport.responseText);

				new Effect.Appear($('floatingWindow'+number), {duration: 0.2});
			} else {
				console.log("hi4");
				$('floatingWindowContent'+number).update(transport.responseText);
			}
		}
	}});
}

Window.prototype.toTop = function(){
	if($('floatingWindow'+this.number))
		$('floatingWindow'+this.number).style.zIndex = WindowManager.topNumber++;
}

Window.prototype.closeMe = function(){
	Event.stopObserving($('floatingWindow'+this.number));

	new Effect.SwitchOff($('floatingWindow'+this.number), {duration: 0.2});

	setTimeout("$('floatingWindow"+this.number+"').remove()", 300);
	WindowManager.registeredWindows[this.number] = null;
	WindowManager.openWindows[this.callClass+";"+this.callMe] = 0;
}

Window.prototype.windowSnap = function(x, y, obj){
	x2 = x;
	y2 = y;

	if(x2 < 0) x2 = 0;
	if(y2 < 0) y2 = 0;

	if(x2 + obj.element.offsetWidth > document.viewport.getDimensions().width) x2 = document.viewport.getDimensions().width - obj.element.offsetWidth;
	if(y2 + obj.element.offsetHeight > document.viewport.getDimensions().height) y2 = document.viewport.getDimensions().height - obj.element.offsetHeight;

	x2 = Math.round(x2 / 20) * 20;
	y2 = Math.round(y2 / 20) * 20;

	return [x2, y2];

}