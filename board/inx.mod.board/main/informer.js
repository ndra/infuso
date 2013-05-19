// @link_with_parent
// @include inx.mod.board.board

inx.mod.board.main.informer = inx.panel.extend({

    constructor:function(p) {
   
        p.items = [{
            html:"<div style='padding:15px 15px 0 15px;' ><b>Я делаю</b>",
            style:{
                border:0
            }
        },{
            type:"inx.mod.board.board.taskList",
            status:1,
            style:{
                border:0
            }
        },{
            type:"inx.mod.board.main.informer.log",
        }];
    
        this.base(p);
    }
         
});