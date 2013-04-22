// @link_with_parent
// @include inx.list

inx.mod.board.task.subtasks = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            border:1,
            padding:0,
            background:"none"
        };
        
        p.data = [{
            text:"Сверстать страницу"
        },{
            text:"Подключить систему управления"
        }];
        
        p.side = [{
            region:"bottom",
            style:{
                background:"none"
            },
            html:"<a href='#' >Добавить задачу</a>"
        }]
        
        this.base(p);
        
    }
     
});