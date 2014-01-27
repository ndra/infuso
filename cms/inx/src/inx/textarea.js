// @include inx.textfield

inx.css(
    ".inx-textarea{box-shadow:0 5px 10px rgba(0,0,0,.1) inset;position:relative;resize:none;font-size:12px;font-family:Arial;border:none;margin:0px;padding:5px;overflow-y:auto;outline:none;}"
);

inx.textarea = inx.textfield.extend({

    autocreate:"<textarea class='inx-textarea' />",
    
    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
    
        if(!p.value)
            p.value = "";
            
        if(!p.style.width && !p.width) {
            p.width = "parent";         
        }   
           
        if(!p.style.height && !p.height) {
            p.style.height = 50;
        }
        
        if(!p.labelAlign) {
            p.labelAlign = "top";
        }
            
        this.base(p);        
        this.on("change","syncLayout");
    },
    
    cmd_destroy:function() {
        if(this.rulerInput) {
            this.rulerInput.remove();
        }
        this.base();
    },
    
    info_textareaContentHeight:function() {    
    
        // Если текстовое поле растягивается автоматически
        if(this.style("height")!="content") {
            return this.info("innerHeight");
        }
        
        // Если текстовое поле растягивается вручную
        if(!$(this.rulerInput).filter(":visible").length) {
        
            this.rulerInput = this.input.clone();
            this.rulerInput.css({
                height:10,
                paddingTop:0,
                paddingBottom:0,
                position:"absolute",
                left:0,
                top:0,
                opacity:.5,
                background:"red"
            });
            this.rulerInput.prependTo(inx.getRuler());
        }
        
        this.rulerInput.prop("value",this.input.prop("value"));
        this.rulerInput.css("width",this.input.width()+17);
        
        var h = this.rulerInput.prop("scrollHeight")+11;
        
        if(h>200) {
            h = 200;
        }
        
        return h;
            
    },
    
    cmd_syncLayout:function() {    
    
        var inputWidth = this.info("innerWidth")-10;    
        this.input.width(inputWidth);
        this.base();
        
        // Чтобы текстовое поле не прыгало
        if(inputWidth>10) {
            var contentHeight = this.info("textareaContentHeight");
            this.input.css("height",contentHeight-10,true);
            this.cmd("setContentHeight",contentHeight);
        }
        
    },
    
    cmd_updateStyle:function() {

        for(var key in this.private_styleChangedKeys) {
            if(key=="fontSize") {
                if(this.input) {
                    this.input.css({
                        fontSize:this.private_style["fontSize"],                        
                    });
                    this.rulerInput = null;
                }
            }
        }
    
        this.base();
    
    },
    
    cmd_keydown:function(e) {
        if(e.which==13) {
            this.task("syncLayout");
        }
        return "stop";
    }

});