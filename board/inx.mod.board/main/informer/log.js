// @link_with_parent
// @include inx.list

inx.mod.board.main.informer.log = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            padding:0,
            border:0
        }
    
        p.loader = {
            cmd:"board/controller/log/getLog"
        }
        
        this.base(p);
        setInterval(inx.cmd(this.id(),"load"),1000*60);
        
        this.on("itemclick",[this.id(),"handleItemClick"])
    },
    
    renderer:function(e,data) {
    
        // Пользователь
        var user = $("<div>").appendTo(e);
        $("<img>").attr("src",data.userpick)
            .attr("align","absmiddle")
            .css({
                marginRight:3
            })
            .appendTo(user);
        $("<span>").html(data.user)
            .css({
                fontSize:11
            }).appendTo(user);
        
        // Текст
        var textContainer = $("<div>").appendTo(e);
        $("<span>").html(data.text+" ").appendTo(textContainer);
        $("<span>").html(" ("+data.taskText+")")
            .css({
                opacity:.7,
                fontStyle:"italic"
            }).appendTo(textContainer);
    
    },
    
    cmd_handleItemClick:function(id) {
    
        var taskID = this.info("item",id).taskID;
    
        var task = inx({
            type:"inx.mod.board.task",
            taskID:taskID
        }).cmd("render");
    }
         
});