// @include inx.tabs

inx.ns("inx.mod.inxdev.example").overview = inx.tabs.extend({

    constructor:function(p) {
        this.a = inx({
            title:"Дерево",
            type:"inx.tree",
            showRoot:false,
            loader:{cmd:"inxdev:example:treeLoader"}
        });
        this.b = inx({
            title:"Список",
            type:"inx.list",
            loader:{cmd:"inxdev:example:listLoader"}
        });
        this.c = inx({
            title:"Редактор php",
            type:"inx.code",
            lang:"php",
            value:"<?\n// Этот код можно редактировать\necho 'hi';\n?>"
        });
        this.d = inx({
            title:"Галлерея",
            type:"inx.gallery",
            data:[
                {img:"/inxdev/img/gallery1.jpg"},
                {img:"/inxdev/img/gallery2.jpg",text:"hi"},
                {img:"/inxdev/img/gallery3.jpg",text:"hallo"}
            ]
        });
        this.call({cmd:"inxdev:example:galleryLoader"},[this.id(),"setGalleryData"])
        this.e = inx({
            title:"Панель",
            type:"inx.panel",
            tbar:[
                {text:"button",icon:"refresh"},
                {type:"inx.pager",total:100},
                "|",
                {type:"inx.select",width:200,loader:{cmd:"inxdev:example:listLoader"}},
                {type:"inx.date"}
            ],
            side:[
                {region:"left",width:50,resizable:true,html:"<div style='padding:10px;'>left</div>"},
                {region:"left",width:200,resizable:true,html:"<div style='padding:10px;'>left</div>"},
                {region:"bottom",height:50,resizable:true,html:"<div style='padding:10px;'>bottom</div>"}
            ]
        })
        p.items = [this.a,this.b,this.c,this.d,this.e];
        this.base(p);
    }

});