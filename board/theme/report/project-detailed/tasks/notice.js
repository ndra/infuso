$(function() {

    $(".r8pze9enrq .edit").click(function() {
    
        var taskID = $(this).attr("data:task");
        var notice = $(this).parents(".r8pze9enrq").find(".notice");
        
        var text = window.prompt("Введите заметку",notice.text());
        if(text===null) {
            return;
        }
        
        mod.cmd({
            cmd:"board_controller_task/updateNotice",
            taskID:taskID,
            notice:text
        });
        
        notice.html(text);
        
    });

})