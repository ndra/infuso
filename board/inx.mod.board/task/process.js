// @link_with_parent
// @include inx.panel

inx.mod.board.task.process = inx.panel.extend({

    constructor:function(p) {      
          
        this.base(p);  
        this.cmd("handleData")
    },
    
    cmd_handleData:function() {
    
        var e = $("<div>");
        
        var data = [
            {title:"<span style='opacity:.5;' >Бэклог</span>"},
            {title:"<b>Выполняется</b>"},
            {title:"<span style='opacity:.5;' >На проверке</span>"},
            {title:"<span style='opacity:.5;' >Выполнено</span>"},
        ];
        
        for(var i in data) {
        
            $("<span>")
                .html(data[i].title)
                .appendTo(e);
                
            $("<span>")
                .html(" &rarr; ")
                .appendTo(e);
                                
        }
        
        this.cmd("html",e);
    
    }
     
});