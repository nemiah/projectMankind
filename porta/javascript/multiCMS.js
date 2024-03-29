
var multiCMSMessages = {
	/*A001:"Bitte geben Sie einen Namen an",
	A002:"Bitte geben Sie eine korrekte E-Mailadresse an",
	A003:"Bitte geben Sie einen Text ein",
	A004:"Die eingegebene E-Mailadresse scheint nicht gültig zu sein",
	
	A005:"Bitte geben Sie eine Straße ein",
	A006:"Bitte geben Sie eine Postleitzahl ein",
	A007:"Bitte geben Sie einen Ort ein",
	A008:"Bitte geben Sie eine Telefonnummer ein",*/
	
	E001:"Kann Formular nicht verarbeiten, keine HandlerID angegeben",
	E002:"Die angegebene HandlerID existiert nicht"//,
	//E003:"Kann Formular nicht verarbeiten, da vom Administrator keine Kontaktadresse eingetragen wurde.",
	
	//G: function(message) {return message;},
	//A100: function(message) {return message;}
}

var multiCMS = {
	formHandler: function(formID, onSuccessFunction){
		if(!$('#'+formID)) alert("Formular nicht gefunden!");
		
		$.ajax({
			url: "/index.php",
			data: "formID="+formID+"&"+$('#'+formID).serialize(),
			success: function(transport){
			
			if(multiCMS.checkResponse(transport) && typeof onSuccessFunction == "function")
				onSuccessFunction(transport);

			},
			
			type: "POST"
		});
	},

	getContent: function(HandlerName, Method, parameters, onSuccessFunction){
		var setString = "";
		jQuery.each(parameters, function(key, value) {
			setString += (setString != '' ? ";;;" : "")+value;
		});
			
		$.ajax({
			url: "/index.php",
			data: "AJAXClass="+HandlerName+"&AJAXMethod="+Method+"&AJAXParameters="+setString,
			success: function(transport){
			
			if(!multiCMS.checkResponse(transport))
				return;
			
			onSuccessFunction(transport);

			},
			
			type: "GET"
		});
	},

	callHandler: function(HandlerName, action, values, onSuccessF){
		var setString = "";
		
		if(typeof values == "string"){
			if(values.indexOf("&") != 0)
				values = "&"+values;
			setString = setString + values;
		}
		
		if(typeof values == "object"){
			jQuery.each(values, function(name, value) {
				setString += "&"+name+"="+value;
			});
		}
		
		$.ajax({
			url: "/index.php",
			data: "formID=nix&HandlerName="+HandlerName+"&action="+action+setString,
			success: function(transport){
			
			if(typeof onSuccessF == "undefined") multiCMS.checkResponse(transport);
				else onSuccessF(transport);

			},
			
			type: "GET"
		});
		
		
		/*setString = "formID=nix&HandlerName="+HandlerName+"&action="+action;

		if(typeof values == "string"){
			if(values.indexOf("&") != 0)
				values = "&"+values;
			setString  = setString + values;
		}


		new Ajax.Request("./index.php",{method:'post', parameters: setString, onSuccess: function(transport){
			if(typeof onSuccessF == "undefined") multiCMS.checkResponse(transport);
			else onSuccessF(transport);
		}});*/
	},
	
	checkResponse: function(transport) {
		if(transport.search(/^error:/) > -1){
			eval("var message = "+transport.replace(/error:/,"")+";");
			alert("Es ist ein Fehler aufgetreten:\n"+message);
			return false;
		}
		if(transport.search(/^alert:/) > -1){
			eval("var message = "+transport.replace(/alert:/,"")+";");
			alert(message);
			return false;
		}
		if(transport.search(/^message:/) > -1){
			eval("var message = "+transport.replace(/message:/,"")+";");
			alert(message);
			return true;
		}
		if(transport.search(/^redirect:/) > -1){
			document.location.href=transport.replace(/redirect:/,"");
		}
		if(transport.search(/^location:/) > -1){
			document.location.href="index.php?p="+transport.replace(/location:/,"");
		}
		if(transport.search(/^permalink:/) > -1){
			document.location.href = transport.replace(/permalink:/,"");
		}
		if(transport.search(/^reload/) > -1){
			document.location.reload();
		}
		if(transport.search(/Fatal error/) > -1){
			alert(transport.replace(/<br \/>/g,"\n").replace(/<b>/g,"").replace(/<\/b>/g,"").replace(/&gt;/g,">").replace(/^\s+/, '').replace(/\s+$/, ''));
			return false;
		}
		return true;
	}
}
