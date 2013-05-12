// @link_with_parent

inx.mod.board.task.more.project = inx.panel.extend({

    constructor:function(p) {
       
        p.items = [{
            type:"inx.list",
            loader:{
                cmd:"board/controller/project/listProjectsSimple"
            },style:{
                maxHeight:100,
                border:0
            }
            
        }];
        
        this.base(p);
    }
    
     
});