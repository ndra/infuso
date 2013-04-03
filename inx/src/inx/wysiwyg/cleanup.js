// @link_with_parent

inx.wysiwyg = inx.wysiwyg.extend({

    cmd_cleanup:function() {
    
        this.cmd("keepSelection");    
        var html = this.info("value");
        html = $.htmlClean(html);
        this.cmd("setValue",html);
        this.cmd("restoreSelection");
        
        inx.msg("clean")
    
    },
    
    cmd_taskCleanup:function() {
        try {
            cleanTimeout(this.cleanupTimeout)
        } catch (ex) {}
        this.cleanupTimeout = setTimeout(inx.cmd(this,"cleanup"),1000);
    }
    
});