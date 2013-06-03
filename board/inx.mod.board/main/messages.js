// @link_with_parent

inx.css(".vb3yl6x {background:red;color:white;padding:4px;}");
inx.css(".vb3yl6x a {color:white;}");

inx.mod.board.main.messages = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            background:"red"
        }       
        this.on("render","getMessages");
        this.base(p);
        
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        })
        
    },
    
    cmd_getMessages:function() {
        this.call({
            cmd:"board/controller/messages/getMessages"
        },[this.id(),"handleData"]);
        
        // Обновляем раз в пять минут
        this.task("getDayActivity",1000 * 60*5);
    },
    
    cmd_handleData:function(data) {
        var e = $("<div>");
        
        for(var i in data) {
            var message = data[i];   
            $("<div>")
                .html(message.text)
                .addClass("vb3yl6x")
                .appendTo(e);
        }
        
        this.cmd("html",e);
    }
         
});