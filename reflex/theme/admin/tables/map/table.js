$(function(){


    var anchors = [ 
        "RightMiddle",
        "LeftMiddle"
    ];

    var color = "#666";

    jsPlumb.importDefaults({
        Connector : [ "Bezier", { curviness:30 } ],
        PaintStyle : { strokeStyle:color, lineWidth:1 },
        HoverPaintStyle : {strokeStyle:"#ec9f2e" },
        EndpointStyle : "Blank",            
        Anchors :  anchors,
        
    });


    $(".j4vuliikh").each(function() {
    
        var target = $("#"+$(this).attr("reflex:target"));
        
        if(target.length) {
        
            jsPlumb.connect({  
                source:$(this), 
                target:$(target),        
                overlays:[ 
                    ["Arrow",{location:1,width:5,length:5}]                    
                ]

            });
            
        }
    
    })
    
    $(window).resize(function() {
        jsPlumb.repaintEverything();
    })
    
})