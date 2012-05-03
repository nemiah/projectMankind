var Interface = {
	buttonGroups: new Object(),

	checkResponse: function(transport) {
		if(transport.responseText == "SESSION EXPIRED"){
			alert(GlobalMessages.A001);
			window.location.reload();
			return false;
		}

		if(transport.responseText.search(/^error:/) > -1){
			eval("var message = "+transport.responseText.replace(/error:/,""));
			alert("An error occured:\n"+message);
			return false;
		}
		if(transport.responseText.search(/^alert:/) > -1){
			eval("var message = "+transport.responseText.replace(/alert:/,""));
			alert(message);
			return false;
		}
		if(transport.responseText.search(/^message:/) > -1){
			eval("var message = "+transport.responseText.replace(/message:/,""));
			alert(message);
			return true;
		}
		if(transport.responseText.search(/Fatal error/) > -1){
			alert(transport.responseText.replace(/<br \/>/g,"\n").replace(/<b>/g,"").replace(/<\/b>/g,"").replace(/&gt;/g,">").replace(/^\s+/, '').replace(/\s+$/, ''));
			return false;
		}
		if(transport.responseText.search(/Warning/) > -1){
			alert(transport.responseText.replace(/<br \/>/g,"\n").replace(/<b>/g,"").replace(/<\/b>/g,"").replace(/&gt;/g,">").replace(/^\s+/, '').replace(/\s+$/, ''));
			return false;
		}

		return true;
	},

	newAjaxGet: function(datatype, target, parameters, onSuccessFunction){
		new Ajax.Request("./interface/getData.php", { parameters: "t="+datatype+";"+target+"&parameters="+JSON.stringify(parameters),method:"post", onSuccess: function(transport){
				if(Interface.checkResponse(transport))
					onSuccessFunction(transport);
		}, onFailure: function(transport) { alert("An error occured: "+transport.responseText); }
		});
	},

	newAjaxSet: function(datatype, target, values, onSuccessFunction){
		new Ajax.Request("./interface/setData.php", { parameters: "t="+datatype+";"+target+"&parameters="+JSON.stringify(values),method:"post", onSuccess: function(transport){
				if(Interface.checkResponse(transport))
					onSuccessFunction(transport);
		}, onFailure: function(transport) { alert("An error occured: "+transport.responseText); }
		});
	},

	unsetButton: function(group){
		if(Interface.buttonGroups[group] == null) return;

		image = Interface.buttonGroups[group][0];
		unsetFunction = Interface.buttonGroups[group][1];

		Interface.buttonGroups[group] = null;

		image.className = "";
		if(typeof unsetFunction == "function") unsetFunction();
	},


	setButton: function(image, group, setFunction, unsetFunction){
		if(typeof Interface.buttonGroups[group] != "undefined" && Interface.buttonGroups[group] != null){
			var temp = Interface.buttonGroups[group][0];
			Interface.unsetButton(group);
			if(temp == image) return;
		}
		
		Interface.buttonGroups[group] = [image, unsetFunction];

		if(typeof setFunction == "function") setFunction();
		image.className = "selected";
	}
}