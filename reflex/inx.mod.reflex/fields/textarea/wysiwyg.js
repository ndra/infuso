// @link_with_parent
// @include inx.wysiwyg

inx.mod.reflex.fields.textarea.wysiwyg = inx.wysiwyg.extend({

    constructor:function(p) {    
        this.base(p);
    },
    
    cmd_insertPhoto:function() {
        this.owner().cmd("openFilemanager",[this.id(),"handleInsertPhoto"]);
    }    
    
})