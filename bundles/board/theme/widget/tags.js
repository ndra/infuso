$(function() {

    inx.on("board/tagsChanged",function(data) {
        var taskID = data.taskID;        
        var container = $(".von4ckgpo6-"+taskID);
        
        inx.call({
            cmd:"board_controller_tag/getWidgetContent",
            taskID:taskID
        },function(data) {
            container.html(data.content);
        })
                        
    });
})