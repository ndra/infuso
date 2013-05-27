$(function() {

    $("#display-subtasks").click(function(){
        var checked = !!$(this).attr("checked");
        
        if(checked) {
            $(".subtask").show()
        } else {
            $(".subtask").hide()
        }
    })

})