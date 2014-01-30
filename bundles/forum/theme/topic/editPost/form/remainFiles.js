$(function(){
    // Кнопка удаления файла
    $(".b-remainFiles-c34dv9u4ud .delete").click(function(e) {
        e.preventDefault();
        var deleteded = $(".b-remainFiles-c34dv9u4ud input[name='deletedattachments']").val();
        
        deleteded = deleteded + $(this).attr("attach:id") + " ";
        
        $(".b-remainFiles-c34dv9u4ud input[name='deletedattachments']").val(deleteded);
        
        $(".attachment-" + $(this).attr("attach:id")).remove();
      
    });    
});
    