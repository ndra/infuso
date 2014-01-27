// @link_with_parent

inx.textfield.getCaret = function(e) {

    e = $(e).get(0);
    if(!e)
        return false;    

    if(typeof(window.getSelection)==="function") {
    
        // Т.к. опера считает перевод строки двумя символами, учитываем это при опрееделнии начала и конца
        start = e.value.substr(0,e.selectionStart).replace(/\r\n/g, "\n").length;
        end = e.value.substr(0,e.selectionEnd).replace(/\r\n/g, "\n").length;
        
        return {start:start,end:end}
    }
        
        var range = document.selection.createRange();
        var start = 0;
        var end = 0;        

        if (range && range.parentElement() == e) {
        
            var len = e.value.length;
            var normalizedValue = e.value.replace(/\r\n/g, "\n");
            var nlen = normalizedValue.length;

            // Create a working TextRange that lives only in the input
            var textInputRange = e.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            var endRange = e.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = nlen;
            } else {
                start = -textInputRange.moveStart("character", -len);
                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = nlen;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                }
            }
        }
        
        return {start:start,end:end};

}
 
inx.textfield.setCaret = function(e,start,end) {

    e = $(e).get(0);
    if(typeof(window.getSelection)==="function") {
    
        var fn = function(str,len) {
            str = str.split("\r");            
            var seek = 0;
            for(var i in str) {
                var x = Math.min(str[i].length,len);
                seek+= x;
                len-= x;
                if(len<=0)
                    return seek;
                    
                seek++;
            }              
        }
    
        e.selectionStart = fn(e.value,start);
        e.selectionEnd = fn(e.value,end);    
        e.focus();
        
    } else {
    
        var selRange = e.createTextRange();
        selRange.collapse(true);
        selRange.moveStart('character', start);
        selRange.moveEnd('character', end-start);
        selRange.select();
        
    }
    
}