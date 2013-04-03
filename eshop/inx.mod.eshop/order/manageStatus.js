// @include inx.dialog

inx.ns("inx.mod.eshop.order").manageStatus = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
    
        p.style = {
            padding:0,
            border:0,
            background:"none"
        }
        this.status = inx({
            type:"inx.select",
            width:100,
            value:p.status,
            loader:{
                cmd:"eshop:edit:getStatuses",
                orderID:p.orderID
            },
            listeners:{
                data:[this.id(),"handleLoad"],
                change:function() {
                    this.owner().items().eq("name","ok").cmd("show");
                }
            }
        });
        p.items = [{
            html:"Изменить статус заказа",
            width:150,
            style:{border:0}
        },this.status,{
            type:"inx.button",
            text:"Сохранить",
            air:true,
            icon:"ok",
            name:"ok",
            hidden:true,
            onclick:[this.id(),"changeStatus"]
        }]
        this.base(p);
    },
    
    cmd_changeStatus:function() {
    
        this.items().eq("name","ok").cmd("hide");
    
        this.call({
            cmd:"eshop:edit:changeStatus",
            orderID:this.orderID,
            status:this.status.info("value")
        },[this.id(),"handleStatusChange"]);
    },
    
    cmd_handleStatusChange:function(p) {
        this.fire("change");
    }
     
});