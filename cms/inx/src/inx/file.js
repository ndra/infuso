// @include inx.button

inx.css(".p9hjmvijmg-area{background:green;opacity:.5;position:absolute;}");

inx.file = inx.button.extend({

    constructor:function(p) {
    
        if(p.icon===undefined) {
            p.icon="upload";
        }
        p.air = true;
        this.base(p);
        this.__defaultIcon = p.icon;
        
        // Быстрая подписка на события
        if(this.oncomplete) this.on("complete",this.oncomplete);
        if(this.onstart) this.on("start",this.onstart);
        if(this.beforeupload) this.on("beforeupload",this.beforeupload);
        
        // Устанавливаем область перетаскивания файлов
        if(p.dropArea===undefined) {        
            p.dropArea = this;
        }
        
        if(p.dropArea) {
            this.cmd("setArea",p.dropArea);
        }
        
    },
    
    /**
     * Создает форму для закачки файла
     * Ифрейм в который будет закачан файл находится внутри этой формы
     * После закачки форма уничтожается
     **/
    createForm:function() {
    
        var id = inx.id();
        this.form = $("<form enctype='multipart/form-data' method='post' style='padding:0px;margin:0px;' />")            
            .attr("action",inx.conf.cmdUrl)
            .appendTo(this.el)
        this.iframe = $("<iframe style='display:none' name='"+id+"' />").appendTo(this.form);
        this.form.attr("target",id);
        
        this.inputFile = $("<input name='file' type='file' />")
            .css({opacity:0,position:"absolute"})
            .appendTo(this.form)
            .change(inx.cmd(this,"submit"));
    },

    cmd_render:function(c) {
        this.base(c);
        this.el.mousemove(inx.cmd(this,"handleMousemove"));
    },
    
    /**
     * При движении мышки надо объектом
     * Перемещает невидимый ифрейм
     **/
    cmd_handleMousemove:function(e) {
    
        if(!this.form) {
            this.createForm();
        }
    
        var x = e.pageX-this.el.offset().left;
        var y = e.pageY-this.el.offset().top;
        this.inputFile.css({
            left:x-this.inputFile.width()+10,
            top:y-this.inputFile.height()/2
        });
    },
    
    cmd_submit:function() {

        if(!this.inputFile.prop("value"))
            return;
    
        this.fire("beforeupload",this.loader);    
        var json = inx.json.encode(this.loader);
        $("<input type='hidden' />").attr({name:"data",value:json}).appendTo(this.form);
        this.iframe.load(inx.cmd(this,"handleLoadForm",this.form));
        this.form.get(0).submit();
        this.form.hide();
        this.createForm();
        this.fire("start");
        this.uploads = this.uploads ? this.uploads+1 : 1;
        this.updateIcon();
    },

    cmd_handleComplete:function(data) {
        this.fire("complete",data);
    },
    
    cmd_handleLoadForm:function(form) {
        var wnd = form.find("iframe").get(0).contentWindow;
        var data = $(wnd.document.body).text();
        data = inx.command.parse(data);
        if(data && data.success) {
            this.cmd("handleComplete",data.data);
        } else {
            inx.msg(data.text,1);
        }
        this.uploads--;
        this.updateIcon();
    },
    
    updateIcon:function() {
        this.cmd("setIcon",this.uploads ? "/inx/pub/inx/file/ajax-loader.gif" : this.__defaultIcon);
    },
    
    cmd_dropFile:function(e) {

        e.preventDefault();
    
        if(!this.loader) {
            inx.msg("inx.file loader is undefined",1);
            return;
        }
    
        this.cmd("hideArea");
        this.fire("beforeupload",this.loader);
        var files = e.originalEvent.dataTransfer.files;
        for (var i = 0; i < files.length; i++) {
            var loader = this.loader;
            loader.file = files[i];
            this.call(loader,[this.id(),"handleComplete"]);
        }
    },
    
    cmd_showArea:function() {

        if(!this.dropZone) {
            this.dropZone = $("<div>").appendTo("body").addClass("p9hjmvijmg-area");
            this.dropZone.bind("drop",inx.cmd(this,"dropFile"));
        }
        var area = this.info("dropArea");
        if(!area) {
            return;
        }
        
        var z = 0;
        area.parents().andSelf().each(function(){
            var nz = parseInt($(this).css("zIndex"));
            if(nz>z) {
                z = nz;
            }
        });
        
        // Если дропария спрятана, выходим
        if(area.filter(":hidden").length) {
            return;
        }
        
        var pos = area.offset();
        this.dropZone.css({
            left:pos.left,
            top:pos.top,
            width:area.outerWidth(),
            height:area.outerHeight(),
            display:"block",
            zIndex:z
        });
            
    },
    
    cmd_hideArea:function() {
        $(this.dropZone).css({display:"none"});
    },
    
    info_dropArea:function() {
        var e = $(this.private_area);
        if(e.prop("nodeName")) {
            return e;        
        }
        e = inx(this.private_area).info("param","el");
        if(e) {
            return e;
        }
    },
    
    cmd_setDropArea:function(e) {
        this.dropArea = e;
    },
    
    cmd_setArea:function(area) {
        this.private_area = area;
        inx.file.dd[this.id()] = true;
    },
    
    cmd_destroy:function() {
        delete inx.file.dd[this.id()];
        this.base();
    }

});

inx.file.handleDragOver = function(e) {
    clearInterval(inx.file.dropAreaInterval);
    inx.file.showArea();
    e.preventDefault();   
}

inx.file.handleDragLeave = function(e) {
    clearInterval(inx.file.dropAreaInterval);
    inx.file.dropAreaInterval = setInterval(inx.file.hideArea,50);
    e.preventDefault();    
}

/**
  * Показывает все области перетаскивания
  **/
inx.file.showArea = function() {
    if(inx.file.dropAreasShown) {
        return;
    }
    for(var i in inx.file.dd) {
        inx(i).cmd("showArea");
    }
    inx.file.dropAreasShown = true;
}

/**
  * Прячет все области перетаскивания
  **/
inx.file.hideArea = function() {
    if(!inx.file.dropAreasShown) {
        return;
    }
    for(var i in inx.file.dd) {
        inx(i).cmd("hideArea");
    }
    inx.file.dropAreasShown = false;
}

inx.file.dd = [];

$(document).on("dragover",inx.file.handleDragOver);        
$(document).on("dragleave",inx.file.handleDragLeave);
$(document).on("drop",inx.file.hideArea);