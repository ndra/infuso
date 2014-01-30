if(!window.ndra) ndra = {};
ndra.bg = {

    sources:[],

    // Добавляет к элементу фон
    add:function(sel,pos,img,p) {
    
        if(!p) p = {};
        if(typeof(p)!="object") p = {mode:"over"}
        if(!p.mode) p.mode = "fixed";

        $(sel).each(function() {
        
            var src = $(this);
            
            // Погдотавливаем родительский элемент
            if(!src.data("incuzd")) {
                ndra.bg.sources.push(src);
                src.css({background:"none",position:"relative"})
					.data("incuzd",true)
					.data("omebj",[]);
            }
            
            if(!src.parent().data("container")) {
                var container = $("<div>").css({position:"absolute"}).prependTo(src.parent());
                src.parent().data("container",container);
            }

			var bgel;
			
            var off = function() {
                var c = [];
                $(src.data("omebj")).each(function() {
					if(this!=bgel) c.push(this);
					else this.remove();
                })
                src.data("omebj",c)
            }
			
            var on = function() {
                if(bgel) off();
                c = $("<div>").appendTo(src.parent().data("container")).css({
                    position:"absolute",
                    overflow:"hidden",
                    background:img
                });
                c.data("pos",pos).data("hover",p.mode!="fixed");
                src.data("omebj").push(c);
                if(p.mode=="fixed") ndra.bg.sync(src);
                else  ndra.bg.realSync(src);
                bgel = c;
            }

			switch(p.mode) {
			    case "fixed":
			    	on();
			        break;
			    case "over":
	                src.mouseenter(on);
	                src.mouseleave(off);
			        break;
			    case "out":
					on();
	                src.mouseenter(off);
	                src.mouseleave(on);
			        break;
			}

        });
    },

	// Планирует синхронизацию
    sync:function() {
        if(!ndra.bg.syncQueued) {
            setTimeout(function() {ndra.bg.realSync()},100);
            ndra.bg.syncQueued = true;
        }
    },

	// Выполняет синхронизацию
    realSync:function(el) {
    
        var items = el || $(ndra.bg.sources);
        items.each(function(x) {
            var source = $(this);
            var o = source.offset();
	        var containerOffset = source.parent().data("container").offset();
            var d = {
                w:source.outerWidth(),
                h:source.outerHeight(),
                x:o.left,
                y:o.top
            };
            $(source.data("omebj")).each(function(){
                ndra.bg.updatePosition(this,d,containerOffset);
            });
        }); 

        ndra.bg.syncQueued = false;
    },

	// Обновляет фон для элемента el в соответствии с параметрами d
    updatePosition:function(el,d,containerOffset) {
    
        if(d.w==0)
            el.css("display","none");
        else
            el.css("display","block");
            
        var pos = (el.data("pos")+"").split(",");
        var top=0,right=0,bottom=0,left=0;
        for(var i in pos) {
        
           switch(i*1) {
                default: var src = 0; break;
                case 0: case 2: var src = d.w; break;
                case 1: case 3: var src = d.h; break;
            }
            var dest = 0;

            var expr = pos[i];
            var m = expr.match(/([+-]?\d+\%?)/g);
            for(var j=0;j<m.length;j++) {
                operation = m[j]+"";
                if(operation.match("%"))
                    dest+=src*parseInt(operation)/100;
                else
                    dest+=parseInt(operation);
            }

            switch(i*1) {
                case 0: left = d.x+dest; break;
                case 1: top = d.y+dest; break;
                case 2: right = d.x+dest; break;
                case 3: bottom = d.y+dest; break;
            }
        }

        el.css({
            top:top-containerOffset.top,
            left:left-containerOffset.left,
            width:right-left,
            height:bottom-top
        });

    },
    
    monitor:function() {
        var items = [$("body")];
        $(items).each(function() {
            var e = $(this);
            var o = e.offset();
            var hash = e.outerWidth()+":"+e.outerHeight()+":"+o.left+":"+o.top;
            if(hash!=e.data("fl2tp1h7")) {
                ndra.bg.sync();
                e.data("fl2tp1h7",hash)
            }
        });
    },
    
    init:function() {
        $(window).resize(ndra.bg.sync);
        ndra.bg.sync();
        $("img").load(ndra.bg.sync);
        setInterval(ndra.bg.monitor,100);
    }
    
}
$(ndra.bg.init)
