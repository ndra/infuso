$(function() {

    var check = function() {
    
        $(".sr3yrzht3j a").each(function() {
            if(this.href==window.location.href) {
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }
        })
    
    }

    setInterval(check,1000);

});