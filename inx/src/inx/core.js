// @link_with_parent

// inline-block
inx.css(".inx-core-inlineBlock{display: -moz-inline-box;display: inline-table;display: inline-block;}");
inx.css(".inx-unselectable{-o-user-select: none;-webkit-user-select: none;-moz-user-select: -moz-none;-khtml-user-select: none;-ms-user-select: none;user-select: none;}");
inx.css(".inx-shadowframe{box-shadow: 0 0 30px rgba(0,0,0,.5);}");
inx.css(".inx-shadow{box-shadow: 0 0 30px rgba(0,0,0,.5);}");
inx.css(".inx-roundcorners{border-radius: 5px;-moz-border-radius: 5px; -webkit-border-radius: 5px;}");

inx.deselect = function() {
    if (window.getSelection) { window.getSelection().removeAllRanges(); }
    else if (document.selection && document.selection.empty)
        document.selection.empty();
}

$(document).mousedown(function(e){
    inx.mouseLButton = true;
    inx.__unselect = !!$(e.target).parents(".inx-unselectable").length;    
    inx.__text = !!$(e.target).parents().andSelf().filter("input,textarea").length; 
    if(inx.__text)
        inx.__unselect = false;
    if(inx.__unselect) {
        inx.deselect();
        e.preventDefault();
        window.focus();
    }
});

$(document).mouseup(function(e){
    inx.mouseLButton = false;
    inx.__unselect = false;
    var u = !!$(e.target).parents(".inx-unselectable").length;
    if(u && !inx.__text) {
        inx.deselect();      
        e.preventDefault();
    }
    inx.__text = false;
});

$(document).mousemove(function(e){
    if(inx.__unselect) {
        inx.deselect();      
        e.preventDefault(); 
    }    
});