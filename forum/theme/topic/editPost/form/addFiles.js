$(function() {
    
    // Если пользователь выброал файл, клонируем инпут чтобы можно было выбрать еще один файл
    $(".files-edit-1b343wxsav .file").change(function() {
        if ($(this).val() != '') {
            var container = $(this).parents(".files-edit-1b343wxsav");
            container.clone(true).insertAfter(container);
        }
    });
    
    // Кнопка удаления файла
    $(".files-edit-1b343wxsav .delete").click(function(e) {
        e.preventDefault();
        var container = $(this).parents(".files-edit-1b343wxsav");
        
        if (container.siblings(".files-edit-1b343wxsav").length > 0) {
            $(container).remove();
        } else {
            $(container).find(".file").val('');
        }
    });
    
});
