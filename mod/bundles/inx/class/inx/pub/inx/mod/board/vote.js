// @include inx.dialog
/*-- /board/inx.mod.board/vote.js --*/


inx.ns("inx.mod.board").vote = inx.panel.extend({

    constructor:function(p) {
        
        p.items = [{
            type:"inx.panel",
            layout:"inx.layout.form",
            name:"top",
            style:{
                border:0
            }
        },{
            type:"inx.panel",
            name:"bottom",
            style:{
                border:0
            }
        }]
    
        p.style = {
            width:500,
            spacing:20,
            border:0,
            background:"white"
        }

        this.on("render","requestData");

        this.base(p);        
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/vote/getCriterias",
            taskID:this.taskID
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
        var taskID = this.taskID;
        
        var top = this.items().eq("name","top");
        var bottom = this.items().eq("name","bottom");
    
        for(var i in data) {
            switch(data[i].type) {
            
                default:
                    top.cmd("add",{
                        type:this.info("type")+"."+"criteria",
                        taskID:this.taskID,
                        criteriaID:data[i].id,
                        label:data[i].title,
                        labelAlign:"left",
                        value:data[i].score,
                        style:{
                            border:0
                        }
                    });
                    break;
                
                case 2:
                    bottom.cmd("add",{
                        type:"inx.checkbox",
                        criteriaID:data[i].id,
                        label:data[i].title,
                        value:!!data[i].score,
                        labelAlign:"top",
                        onchange:function() {
                            var val = this.info("value");
                            this.call({
                                cmd:"board/controller/vote/vote",
                                taskID:taskID,
                                criteriaID:this.criteriaID,
                                score:val
                            });
                        }
                    });
                    break;
            
            }
        }
    }
         
});

/*-- /board/inx.mod.board/vote/criteria.js --*/


inx.css(".pu03plbnk-smile {display:inline-block;width:60px;text-align:center;height:38px;font-size:32px;opacity:.4;}")
inx.css(".pu03plbnk-smile:hover {opacity:1;font-weight:bold;}");
inx.css(".pu03plbnk-smile.active {opacity:1;font-weight:bold;}");

inx.mod.board.vote.criteria = inx.panel.extend({

    constructor:function(p) {    
        if(!p.style) {
            p.style = {};
        }
        p.style.background = "none";
        this.private_value = p.value;
        this.base(p); 
        this.on("render","drawCriteria");
    },
        
    cmd_drawCriteria:function() {
        var cmp = this;
        var e = $("<div>");
        var smiles = [":(:(",":(",":|",":)",":):)"];
        for(var i=1;i<=5;i++) {
            var smile = $("<span>").html(smiles[i-1])
                .addClass("pu03plbnk-smile")
                .data("score",i)
                .click(function() {
                    var score = $(this).data("score");
                    cmp.cmd("vote",score);
                }).appendTo(e);
            if(this.private_value==i) {
                smile.addClass("active");
            }
        }
        this.cmd("html",e);
    },
    
    cmd_setValue:function(value) {
        this.private_value = value;
        this.task("drawCriteria");
    },
    
    cmd_vote:function(score) {
        this.call({
            cmd:"board/controller/vote/vote",
            taskID:this.taskID,
            criteriaID:this.criteriaID,
            score:score
        });
        this.cmd("setValue",score);
    }
         
});

