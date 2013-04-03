// @link_with_parent
// @include inx.form

inx.mod.mysql.admin.query = inx.form.extend({

    constructor:function(p) {
        this.query = inx({
            type:"inx.textarea",
            region:"top"
        });
        this.result = inx({
            type:"inx.panel",
            padding:10,
            autoHeight:true,
            border:0
        });
        p.items = [
            this.query,
            {type:"inx.button",text:"Пошел",onclick:[this.id(),"sendQuery"]},
            this.result
        ];
        p.padding = 10;
        this.base(p);
        inx.hotkey("ctrl+enter",this,"sendQuery");
    },
    
    cmd_sendQuery:function() {
        this.call({cmd:"mysql:admin:sendQuery",query:this.query.info("value")},[this.id(),"handleQuery"]);
    },
    
    cmd_handleQuery:function(data) {
        this.result.cmd("html",data);
    }

});
