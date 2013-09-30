// @link_with_parent
// @include inx.mod.board.board

inx.mod.board.main.informer = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            padding:15,
            spacing:10,
            vscroll:true,
            background:"#ededed"
        }
   
        p.items = [{
            html:"<b>Я делаю</b>",
            style:{
                border:0,
                height:20
            }
        },{
            type:"inx.mod.board.taskList",
            status:1,
            style:{
                border:0,
                padding:0
            }
        },{
            type:"inx.mod.board.comments",
            style:{
                padding:1,
                border:0
            }
        }];
    
        this.base(p);
    }
         
});