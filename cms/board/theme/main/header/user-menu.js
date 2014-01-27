$(function() {

    setInterval(function() {
    
        mod.cmd({
            cmd:"board/controller/messages/getUnreadMessagesNumber"
        },function(ret) {
            $(".jmi8th58od .messages").text(ret);
        })
    
    },60 * 1000)

});