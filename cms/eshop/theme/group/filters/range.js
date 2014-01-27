$(function(){

    $(".amn8j5ujfk").bind("input",function(){
        var tag = $(this).attr("filter:tag");
        mod.cmd({
            cmd:"reflex:filter:controller:updateTag",
            tagID:tag,
            key:$(this).attr("name"),
            val:$(this).val(),
        });
    })

})