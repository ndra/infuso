// @link_with_parent

inx.css(".a2op9st4e2jw {font-size:11px;background:#ffeebb;max-width:200px;position:absolute;padding:10px;}");

inx.help = {

    show:function(id,x,y) {
        var help = inx(id).info("param","help");
        if(!help) return;
        if(!inx.help.e)
            inx.help.e = $("<div>")
                .addClass("a2op9st4e2jw")
                .addClass("inx-shadowframe")
                .appendTo("body")
                .css({zIndex:inx.conf.z_index_message})
        inx.help.e.fadeIn(300).css({left:x+10,top:y+10}).html(help+"");
    },

    hide:function() {
        if(inx.help.e)
            inx.help.e.hide();
    }

}
