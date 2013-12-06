$(function() {

    mod.on("board/showLog",function(params) {
        mod.cmd({
            cmd:"board/controller/report/chartDetails",
            params:params
        },function(r) {
        
            $(".dcyy6ydrbx .details").get(0).innerHTML = r;
            
        });
    });

})