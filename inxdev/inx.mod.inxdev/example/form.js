// @include inx.form,inx.checkbox

inx.ns("inx.mod.inxdev.example").form = inx.form.extend({

    constructor:function(p) {
        p.items = [{
            label:"Строка",
            name:"string",
            value:"Капиллярное поднятие структурно опускает замок складки, где на поверхность выведены кристаллические структуры фундамента."
        },{
            label:"Чекбокс",
            name:"checkbox",
            type:"inx.checkbox"
        },{
            label:"Еще чекбокс",
            name:"checkbox2",
            type:"inx.checkbox"
        },{
            label:"Текстовое поле",
            type:"inx.textarea",
            name:"textarea",
            labelALign:"left"
        },{
            label:"Селект",
            type:"inx.select",
            loader:{cmd:"inxdev:example:listLoader"},
            name:"select"
        },{
            label:"Дата",
            type:"inx.date",
            name:"date"
        },{
            label:"Код PHP",
            type:"inx.code",
            value:"<? echo 123; ?>",
            lang:"php",
            name:"code"
        }];
        
        p.tbar = [
            {text:"get data",onclick:[this.id(),"showData"]}
        ];
        
        p.side = [{
            width:20,
            region:"right",
            resizable:true,
            background:"#ededed"
        }];
        
        if(!p.style)
            p.style = {}
        p.style.vscroll = true
        p.style.hscroll = true
        this.base(p);
    },
    
    cmd_showData:function() {
        inx.msg(this.info("data"));
    }

});