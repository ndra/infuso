// @link_with_parent

inx.css(".qmazoce2d {position:absolute;top:4px;overflow:hidden;text-overflow:ellipsis;font-style:italic;}");
inx.css(".qmazoce2d-separator { width:6px; height:22px; cursor:move; opacity:.2; top:0; position:absolute; }");

inx.list.head = inx.panel.extend({

    constructor:function(p) {
        p.height = 22;
        p.style = {
            background:"#ededed"
        }
        p.private_colWidth = {};
        this.base(p);
    },
    
    cmd_render:function(c) {
    
        this.base(c);
        
        this.roller = $("<div>")
            .css({
                position:"absolute",
                whiteSpace:"nowrap"
            }).appendTo(this.__body);
        this.__body
            .css({
                cursor:"pointer"
            }).mouseover(inx.cmd(this,"handleMouseOver"))
            .mouseout(inx.cmd(this,"handleMouseOut"));
    },
    
    cmd_handleMouseOver:function(e) {
        var target = $(e.target);
        var col = this.owner().info("col",target.data("name"),"title");
        if(col) {
            this.help = col;
        }
              
        if(!target.data("name"))
            return;
            
        target.css({
            textDecoration:"underline"
        });
    },
    
    cmd_handleMouseOut:function(e) {
    
        this.help = "";
        var target = $(e.target);
        if(!target.data("name"))
            return;
        
        target.css({
            textDecoration:"none"
        });
    },
    
    /**
     * Устанавливает данные колонок
     * Данные должны быть в формате 
     **/
    cmd_setColData:function(cols) {
        this.cols = [];
        var left = 0;
        for(var i in cols) {
            var col = cols[i];
            var width = parseInt(col.width);
            if(!width) {
                switch(col.type) {
                    case "image": width = 18; break;
                    default: width = 100; break;
                }
            }
            this.cmd("setColWidth",col.name,width,true);
            col.title = col.title || "";
            this.cols[col.name] = col;
        }
        this.task("renderCols");
        this.task("rebuildHash");
    },
    
    
    /**
     * Перестраивает хэш ширины столбцов
     * Должно вызываться при каждом изменении ширины
     **/
    cmd_rebuildHash:function() {
        this.hash = inx.crc32(this.private_colWidth);
    },
    
    /**
     * Возвращает хэш, зависящий от ширины столбцов
     **/
    info_hash:function() {
        return this.hash;
    },
    
    /**
     * Возвращает ширину колонки по ее имени
     **/
    info_colWidth:function(name) {
        var ret =  this.private_colWidth[name];
        if(ret < 16 + 8) {
            ret = 16 + 8;
        }
        return ret;        
    },
    
    /**
     * Возвращает суммарную ширину колонок
     **/
    info_totalWidth:function() {
        var ret = 0;
        for(var i in this.cols) {
            ret += this.info("colWidth",this.cols[i].name);
        }
        return ret;
    },
    
    /**
     * Возвращает отступ колонки от левого края
     **/
    info_colLeft:function(name) {
        var left = this.owner().style("padding");
        for(var i in this.cols) {
            if(this.cols[i].name==name) {
                break;
            }
            left += this.info("colWidth",this.cols[i].name);
        }
        return left;
    },
    
    /**
     * Устанавливает ширину колонки
     **/
    cmd_setColWidth:function(name,width,init) {
        if(this.private_colWidth[name] && init) {
            return;
        }
        this.private_colWidth[name] = width;
        this.task("renderCols");
        this.task("rebuildHash");
    },
    
    /**
     * Возвращает данные колонок (массив)
     **/
    info_cols:function() {
        return this.cols;
    },

    /**
     * Возвращает данные столбца
     **/    
    info_col:function(name) {
        return this.cols[name];
    },
    
    /**
     * Переводит смещение от левого края в имя колонки
     * Т.е. в какую колонку мы попадем, если нажмен на расстоянии x от левого края
     **/
    info_offsetToName:function(x) {
        var left = 0;
        for(var i in this.cols) {
            left+=this.info("colWidth",this.cols[i].name);
            if(left>x)
                return this.cols[i].name;
        }
    },

    /**
     * Рендерит заголовок таблицы
     **/
    cmd_renderCols:function() {
    
        if(!this.roller)
            return;
    
        this.roller.html("");
        var cols = this.info("cols");
        for(var i in cols) {
        
            var name = cols[i].name;
            $("<div>")
                .addClass("qmazoce2d")
                .css({
                    width:this.info("colWidth",name),
                    left:this.info("colLeft",name) - 1
                 }).html(cols[i].title)
                .appendTo(this.roller)
                .data("name",name);
                
            var sep = $("<div>")
                .addClass("qmazoce2d-separator")
                .css({                    
                    background:"url("+inx.conf.url+"/inx/list/separator.gif)",
                    left:this.info("colLeft",name)+this.info("colWidth",name) - 7
                }).appendTo(this.roller)
                .data("name",cols[i].name);
                
            inx.dd.enable(sep,this,"drag",{name:cols[i].name});
        }
    },
    
    /**
     * Обработчик перетаскивания
     **/
    cmd_drag:function(params) {
        this.cmd("setColWidth",params.name,this.info("colWidth",params.name)+params.dx);
        if(params.phase=="stop")
            this.owner().cmd("updateAll");
    },
   
    /**
     * Обработчик перетаскивания
     **/
    cmd_setScroll:function(left) {
        if(this.roller)
            this.roller.css({left:-left});
    },
    
    /**
     * При нажатии мыши на заголовок, посылаем уведомление родительскому объекту
     **/
    cmd_mousedown:function(e) {
        var name = $(e.target).parents().andSelf().filter(".qmazoce2d").data("name");
        this.owner().fire("headerClick",name);
    }    

});

//inx.list.head.getHeaderElement = function() {
//}