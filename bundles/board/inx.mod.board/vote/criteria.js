// @link_with_parent

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