// @link_with_parent

inx.css(".vb3yl6x {position:relative; background:red linear-gradient(#ff4444,red);color:white;padding:4px;}");
inx.css(".vb3yl6x a {color:white;}");
inx.css(".vb3yl6x .delete {cursor:pointer;}");

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
        this.task("getMessages",1000 * 60*5);
    },
    
    cmd_handleData:function(data) {
        var container = $("<div>");
        
        var cmp = this;
        
        for(var i in data) {
            var message = data[i];   
            var e = $("<div>")
                .html(message.text)
                .addClass("vb3yl6x")
                .appendTo(container)
                .data("message-hash",message.hash);
                
            $("<img>").attr("src",inx.img("delete"))
                .appendTo(e)
                .addClass("delete")
                .css({
                    position:"absolute",
                    right:3,
                    top:3
                }).click(function() {
                    var hash = $(this).parent().data("message-hash");
                    cmp.cmd("hideMessage",hash);
                    cmp.call({
                        cmd:"board/controller/messages/hideMessage",
                        hash:hash
                    })
                });
        }
        
        this.cmd("html",container);
    },
    
    cmd_hideMessage:function(hash) {
            
        var cmp = this;
        var e = this.__body.children().children().children();
        e.each(function() {
            if($(this).data("message-hash") == hash) {
                $(this).fadeOut("fast",function() {
                    cmp.cmd("syncLayout");
                });
            }
        })
    }
         
});