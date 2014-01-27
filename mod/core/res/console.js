$(function() {
    $("body").ajaxError(function(event, request, settings){
        cs.log("Error requesting page " + settings.url,1);
        cs.log(request.responseText,1);
        var data = settings.data.split("=");
        var mod = data[1];
        cs.enableButtons();
    });
});

cs = {

    log:function(text,error) {
        var e = $("<div />").html(text+"").addClass("log-message").appendTo("#log");
        if(error)
            e.css({color:"red"});
    },

    clearLog:function() {
        $("#log").html("");
    },
    
	disableButtons:function() {
	    $("input").attr("disabled","disabled");
	},
	
	enableButtons:function() {
	    $("input").attr("disabled","");
	},
    
    updateStep:function(mod) {
        cs.disableButtons();
        $.post(
            "/mod/?cmd=update",
            {mod:mod},
            function(data){cs.handleUpdateStep(data,mod)},
            "json"
        );
    },

    handleUpdateStep:function(data,mod) {
        if(data && data.next==true) {
	        var messages = data.messages;
	        for(var i in messages)
	            cs.log(messages[i].text,messages[i].error);
            cs.updateStep(mod+1);
		} else {
			cs.handleUpdateDone();
		}
    },
    
    handleUpdateDone:function() {
    	cs.log("<div style='color:green;'>Update done</div>",0);
    	cs.linkStep(0);
    	cs.enableButtons();
	},
    
    linkStep:function(step) {
        cs.disableButtons();
        $.post(
            "/mod/?cmd=relink",
  			{ step:step },
			function(data) {
				cs.handleLinkStep(data,step)
			},
			"json"
        );
    },

    handleLinkStep:function(data,step) {
        if(data && data.next==true) {
	        var messages = data.messages;
	        for(var i in messages)
	            cs.log(messages[i].text,messages[i].error);
            cs.linkStep(step+1);
		} else {
			cs.handleLinkDone();
		}
    },
    
    handleLinkDone:function() {
    	cs.log("<div style='color:green;'>Relink done</div>",0);
    	cs.enableButtons();
	}

}
