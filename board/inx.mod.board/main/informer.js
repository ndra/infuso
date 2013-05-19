// @link_with_parent
// @include inx.mod.board.board

inx.mod.board.main.informer = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            padding:15,
            spacing:10
        }
   
        p.items = [{
            html:"<b>Я делаю</b>",
            style:{
                border:0
            }
        },{
            type:"inx.mod.board.board.taskList",
            status:1,
            style:{
                border:0,
                padding:0
            }
        },{
            type:"inx.mod.board.main.informer.log",
        }];
    
        this.base(p);
    }
         
});