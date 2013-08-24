// @link_with_parent

inx.css(".ubd4v2nfv-table {table-layout:fixed;width:10px;}");
inx.css(".ubd4v2nfv {text-overflow:ellipsis;overflow:hidden;padding:4px;white-space: nowrap;vertical-align:middle;}");
inx.css(".ubd4v2nfv:hover {background:rgba(0,0,0,.1);}");

inx.list.gridItem = inx.panel.extend({

    constructor:function(p) {
    
        p.fullData = p.data;
        p.data = p.data.data;
    
        if(!p.style) {
            p.style = {};
        }
            
        p.style.border = 0;
        p.style.padding = 0;
        
        p.style.autoWidth = false;
        this.on("dblclick","extendEvent");
        this.on("click","extendEvent");
        this.base(p);
    },
    
    /**
     * Добавляет в событие мыши информацию о колонке в которую мы нажали
     **/
    cmd_extendEvent:function(e) {
    
        var x = e.pageX+this.__body.scrollLeft() - this.__body.offset().left;
        
        // Имя колонки
        e.colName = this.head.info("offsetToName",x);
        e.col = e.colName;
        
        // Данные колонки
        e.cellData = this.data[e.colName];
        
        // Действие
        switch(e.type) {
            case "click":
                if(e.cellData) {
                    e.action = e.cellData.click;
                }
                break;
            case "dblclick":
                if(e.cellData) {
                    e.action = e.cellData.dblclick;
                }
                break;
        }
        
        e.cellX = e.pageX;
        e.cellY = e.pageY;
    },
    
    info_rowWidth:function() {
        return this.info("regionSize","left") + this.head.info("totalWidth") + this.info("regionSize","right");
    },
    
    info_colOffset:function() {
        return this.info("regionSize","left");
    },
    
    cmd_render:function() {
    
        this.base();       

        var width = this.info("rowWidth");  
        this.cmd("width",width,"ya23f9bokv23");
        var cols = this.head.info("cols");
        
        var e = $("<tr>");        
        
        if(this.fullData.css) {
            for(var i in this.fullData.css) {
                e.css(i,this.fullData.css[i]);
            }
        }
            
        var table = $("<table>").addClass("ubd4v2nfv-table");
        e.appendTo(table)
        this.cmd("html",table);
        
        this.tr = e;
        for(var i in cols) {
        
            var name = cols[i].name;
            var cellData = this.data[cols[i].name];
            
            if(!(cellData instanceof Object)) {
                cellData = {
                    text:cellData
                };
            }            
            
            var td = $("<td>")
            .addClass("ubd4v2nfv")
            .css({                
                left:this.head.info("colLeft",name),
                textAlign:this.head.info("colAlign",name)
            }).appendTo(e);    
            
            // Стиль ячейки    
            if(cellData.css) {
                td.css(cellData.css);
            }

            // Ширина css
            var width = this.head.info("colWidth",name) - 8;
            td.width(width);
        
            // Контент ячейки
            if(cellData.text || cellData.text+""==="0") {    
            
                if(cols[i].type=="image") {
                    $("<img>").css({position:"relative",display:"block",width:16,height:16}).attr("src",inx.img(cellData.text)).appendTo(td);
                } else {
                    td.html(cellData.text+"");
                }
                                    
            }
            
            if(cellData.inx) {
            
                var cmp = inx(inx.deepCopy(cellData.inx));                
                cmp.cmd("render");
                cmp.cmd("appendTo",td);                
                cmp.setOwner(this);
                
                var w = this.head.info("colWidth",name)-8;
                cmp.cmd("width",w);
            }
            
        }

    },
    
    cmd_syncLayout:function() {
        this.base();
        this.style("width",this.info("rowWidth"));
    }

});