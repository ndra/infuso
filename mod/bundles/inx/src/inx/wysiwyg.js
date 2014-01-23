// @include inx.panel

inx.wysiwyg = inx.panel.extend({

    constructor:function(p) {
        p.autoHeight = false;
        if(!p.height)
            p.height = 200;
            
        p.bbar = [{           
            text:"П",
            menu:[{
                text:"Обычный текст",
                onclick:inx.cmd(this.id(),"execCommand","formatblock","p")
            },{
                text:"Цитата",
                onclick:inx.cmd(this.id(),"execCommand","formatblock","blockquote")
            },{
                text:"Код",
                onclick:inx.cmd(this.id(),"execCommand","formatblock","pre")
            },{
                text:"Заголовок 1",
                onclick:inx.cmd(this.id(),"execCommand","formatblock","h1")
            },{
                text:"Заголовок2",
                onclick:inx.cmd(this.id(),"execCommand","formatblock","h2")
            }]            
        },
        "|",
        {
            text:"<b>&nbsp;B&nbsp;</b>",
            onclick:inx.cmd(this.id(),"execCommand","bold")
        },{
            text:"<i>&nbsp;I&nbsp;</i>",
            onclick:inx.cmd(this.id(),"execCommand","italic")
        },{
            text:"<strike>&nbsp;T&nbsp;</strike>",
            onclick:inx.cmd(this.id(),"execCommand","strikethrough")
        },
        "|",
        {
            text:"ul",
            onclick:inx.cmd(this.id(),"execCommand","insertunorderedlist")
        },{
            text:"ol",
            onclick:inx.cmd(this.id(),"execCommand","insertorderedlist")
        },
        "|",
        {
            text:"Таблица",
            menu:[{
                    text:"Вставить таблицу",
                    onclick:[this.id(),"showTableDlg"]                
                },"|",{
                    text:"Добавить строку сверху",
                    onclick:[this.id(),"insertRowAbove"]                
                },{
                    text:"Добавить строку снизу",
                    onclick:[this.id(),"insertRowBelow"]                
                },{
                    text:"Добавить столбец слева",
                    onclick:[this.id(),"insertColumnLeft"]                
                },{
                    text:"Добавить столбец справа",
                    onclick:[this.id(),"insertColumnRight"]                
                },"|",{
                    text:"Добавить заголовок",
                    onclick:[this.id(),"addHead"]
                },{
                    text:"Удалить заголовок",
                    onclick:[this.id(),"deleteHead"]
                },"|",{
                    text:"Удалить столбец",
                    onclick:[this.id(),"deleteColumn"]
                },{
                    text:"Удалить строку",
                    onclick:[this.id(),"deleteRow"]
                },{
                    text:"Удалить таблицу",
                    onclick:[this.id(),"deleteTable"]
                }]
        },{
            text:"Фото",
            onclick:[this.id(),"insertPhoto"]
        },"|",{
            text:"Очистить",
            onclick:[this.id(),"cleanup"]
        }]
        
        this.base(p);
    },
    
    cmd_insertPhoto:function() {
        this.cmd("handleInsertPhoto",{
            src:'/'
        });
    },
    
    cmd_handleInsertPhoto:function(url) {
        var html = "<img src='"+url+"'/>";
        this.cmd("execCommand","inserthtml",html);
    },
    
    cmd_render:function(c) {
        this.base(c);
        
        this.__body.css({overflow:"hidden"})
        
        this.iframe = $("<iframe frameborder='0' scrolling='auto' >");
        this.iframe.appendTo(this.__body);
        
        setInterval(inx.cmd(this.id(),"taskRunner"),1000);
        //this.cmd("taskRunner");
    },
    
    cmd_taskRunner:function() {
        
        // Добавляем css со стилями
        if(!this.docCreated) {
        
            this.docCreated = true;
        
            var doc = this.info("doc");
            doc.open();
            doc.writeln('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
            var css = inx.path("inx.wysiwyg")+"blank.css";
            doc.write("<html><head><link rel='stylesheet' type='text/css' href='"+css+"' /></head><body></body></html>");
            doc.close();
            doc.designMode = "on";            

            this.iframe.focus(inx.cmd(this,"handleFocusFrame"));
            this.info("window").focus(inx.cmd(this,"handleFocusFrame"));               
            $(this.info("window")).blur(inx.cmd(this,"handleBlurFrame"));
            
        }
        
        // Следим за текущей нодой
        this.task("observeCurrentNode");
        
    },
    
    cmd_handleFocusFrame:function() {
        this.frameFocused = true;
        this.cmd("focus");
    },
    
    cmd_handleBlurFrame:function() {
        this.cmd("taskCleanup");
        this.frameFocused = false;
        this.cmd("blur");
    },
    
    cmd_setValue:function(html) {
        $(this.info("doc").body).html(html);
    },
    
    cmd_syncLayout:function() {
        this.base();
        this.iframe.css({
            width:this.info("bodyWidth"),
            height:this.info("bodyHeight")
        });
    },
    
    info_doc:function() {
        return this.iframe.get(0).contentWindow.document;
    },
    
    info_window:function() {
        return this.iframe.get(0).contentWindow;
    },
    
    info_value:function() {
        //inx.msg($(this.info("doc")).find("html").html())
        return $(this.info("doc")).find("body").html();
    },
    
    // ------------------------------------------------------------------ Команды
    
    cmd_focusFrame:function() {
        this.iframe.get(0).contentWindow.focus();
    },
    
    cmd_execCommand:function(cmd,param) {   
    
        this.cmd("focusFrame");
    
        var doc = this.info("doc");                                
        if (cmd == 'inserthtml' && $.browser.msie) {
            doc.selection.createRange().pasteHTML(param);    
        } else if (cmd == 'formatblock' && $.browser.msie) {
            doc.execCommand(cmd, false, '<' +param + '>');
        } else if (cmd == 'indent' && $.browser.mozilla) {
            doc.execCommand('formatblock', false, 'blockquote');
        } else {                                            
            doc.execCommand(cmd, false, param);
        }
        
        this.cmd("taskCleanup");
        
    },
    
    // ------------------------------------------------------------------ Таблица
    
    cmd_showTableDlg:function() {
        inx({
            type:"inx.wysiwyg.tableDlg",
            listeners:{
                insertTable:[this.id(),"insertTable"]
            }
        }).cmd("render");
    },
    
    cmd_insertTable:function(data) {
        var html = "<table><tbody></tbody>";
        for(var row=0;row<data.rows;row++) {
            html+= "<tr>";
            for(var col=0;col<data.cols;col++)
                html+= "<td>&nbsp;</td>";
            html+= "</tr>";
        }
        html+= "</table>";
        this.cmd("execCommand","inserthtml",html);  
    },
    
    cmd_deleteTable: function() {
        this.info("current","table").remove();
    },
    
    cmd_deleteRow: function() {
        this.info("current","tr").remove();
    },
    
    cmd_deleteColumn: function() {    
        var index = this.info("current","td").get(0).cellIndex; 
        this.info("current","table").find('tr').each(function() {   
            $(this).find('td').eq(index).remove();
        });     
    },    
    
    cmd_addHead: function() {
    
        var table = this.info("current","table");    
        if (table.find('thead').length) {
            this.cmd("deleteHead");
        } else {
            var tr = table.find('tr').first().clone();
            tr.find('td').html('&nbsp;');
            var thead = $('<thead></thead>');
            thead.append(tr);
            table.prepend(thead);
        }
    }, 
       
    cmd_deleteHead: function() {
        var table = this.info("current","table");
        table.find('thead').remove(); 
    },  
    
    cmd_insertRowAbove: function() {
        this.cmd("insertRow","before");
    },       
         
    cmd_insertRowBelow: function() {
        this.cmd("insertRow","after");    
    },
    
    cmd_insertColumnLeft: function() {
        this.cmd("insertColumn","before");
    },
    
    cmd_insertColumnRight: function() {
        this.cmd("insertColumn","after");
    },  
     
    cmd_insertRow: function(type) {
        var currentTR = this.info("current","tr");
        var newTR = currentTR.clone();
        newTR.find('td').html('&nbsp;');
        if (type == 'after')
            currentTR.after(newTR);
        else
            currentTR.before(newTR);        
    },
    
    cmd_insertColumn: function(type) {
    
        var index = 0;
        var currentTD = this.info("current","td");
        var currentTR = this.info("current","tr");
        var currentTable = this.info("current","table");
        currentTD.addClass('current');
                        
        currentTR.find("td").each(function(i,s) {
            if ($(s).hasClass('current'))
                index = i;
        });
        
        currentTable.find("tr").each(function(i,s) {   
            var current = $(s).find('td').eq(index);    
            var td = current.clone();   
            td.html('&nbsp;');
            if (type == 'after')
                $(current).after(td);
            else
                $(current).before(td);                
        });            
    }
    
});
