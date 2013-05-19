// @link_with_parent
// @include inx.list

inx.mod.board.main.informer.log = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            border:0
        }
    
        p.loader = {
            cmd:"board/controller/log/getLog"
        }
        this.base(p);
    }
         
});