$(function() { 

    var anchors = [ 
        "LeftMiddle",
        "RightMiddle"
    ];

    var color = "#666";

    jsPlumb.importDefaults({
        Connector : [ "Bezier", { curviness:70 } ],
        PaintStyle : { strokeStyle:color, lineWidth:2 },
        HoverPaintStyle : {strokeStyle:"#ec9f2e" },
        EndpointStyle : "Blank",            
        Anchors :  anchors,
        
    });

    $(".axxvozzes4").each(function() {
        var parents = $(this).attr("data:parents").split(" ");
        for(var i in parents) {
            var parent = $("#operation-"+(parents[i]));
            if(parent.length) {            
            
            jsPlumb.connect({  
                    source:$(this), 
                    target:parent    
                });
            }        
            
        }
    });
    
    $(window).resize(function() {
        jsPlumb.repaintEverything();
    })
    
    jsPlumb.repaintEverything();

});