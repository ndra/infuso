// @link_with_parent

inx.wysiwyg = inx.wysiwyg.extend({
        
    info_currentNode:function() {
        var ret = null;
        try{
            if (window.getSelection) {
                ret =  this.info("selection").getRangeAt(0).startContainer;                
            }else {
                if (document.selection)
                    ret = this.info("selection").parentElement();
            }
        } catch(ex) {}
        return $(ret);
    },
    
    info_selection:function() {
        if (this.iframe.get(0).contentWindow.getSelection)
            return this.iframe.get(0).contentWindow.getSelection();
        else if (this.iframe.get(0).contentWindow.document.selection)
            return this.iframe.get(0).contentWindow.document.selection.createRange(); 
    },
    
    cmd_observeCurrentNode:function() {
    
        //inx.msg(this.info("doc").head.innerHTML.length)
    
        var node = this.info("currentNode");
        
        /* Показываем текущий тэг
        var x = [];
        $(node.parents().andSelf()).each(function(){
            x.push(this.nodeName);
        })
        inx.msg(x.join("/")) */
        
        var elements = ["table","tr","td"];
        
        for(var i in elements) {
            var name = elements[i];

            var e = $(node).filter(name);

            if(!e.length)
                var e = $(node).parents(name).first();
            if(e.length)
                this.cmd("setCurrentElement",name,e);
        }

    },
    
    current:{},
    
    cmd_setCurrentElement:function(name,e) {
        this.current[name] = e;
    },
    
    info_current:function(name) {
        return $(this.current[name]);
    },
    
    cmd_keepSelection:function() {
    
        if(!this.frameFocused)
            return;
            
        this.cmd("execCommand","inserthtml","#rnwlsj4abj4es38vr7fe#");
    },
    
    cmd_restoreSelection:function() {
    
        if(!this.frameFocused)
            return;
    
        var html = this.info("value");
        html = html.replace("#rnwlsj4abj4es38vr7fe#","<span id='rnwlsj4abj4es38vr7fe' >*</span>");
        this.cmd("setValue",html);
        
        var node = $(this.info("doc")).find("#rnwlsj4abj4es38vr7fe");
        this.cmd("setFocusNode",node);
    },
    
    cmd_setFocusNode: function(node) {
    
        var target = $(node).get(0);
        var rng, sel;
        var doc = this.info("doc");
        var wnd = this.info("window");
        
        if (doc.createRange) {
            rng = doc.createRange();
            rng.selectNode(target)
            sel = wnd.getSelection();
            sel.removeAllRanges();
            sel.addRange(rng);
        } else {
            var rng = doc.body.createTextRange();
            rng.moveToElementText(target);
            rng.select();
        }

        this.cmd("execCommand","delete");
    }
    
});