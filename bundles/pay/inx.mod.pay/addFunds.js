// @include inx.dialog

inx.ns("inx.mod.pay").addFunds = inx.dialog.extend({

    constructor:function(p) {
    
        p.width = 300;
        
        this.form = inx({
            labelWidth:100,
            style:{
                border:0
            },
            type:"inx.form",
            items:[{
                type:"inx.textfield",
                label:"Сумма",
                width:50,
                name:"amount",
                value:0                
            },{
                type:"inx.button",
                text:"Пополнить",
                labelAlign:"left",
                onclick:[this.id(),"addFunds"]
            }]
        });
        
        p.items = [this.form];
        
        this.base(p);
    },
    
    cmd_addFunds:function() {
        this.call({
            cmd:"pay_admin:addFunds",
            userID:this.userID,
            amount:this.info("data")["amount"]
        },[this.id(),"handleAdd"]);
    },
    
    cmd_handleAdd:function() {
        this.bubble("refresh");
        this.task("destroy");
    }
    
     
});