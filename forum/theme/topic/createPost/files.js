$(function() {
    
    // Если пользователь выброал файл, клонируем инпут чтобы можно было выбрать еще один файл
    $(".e6fqus98fp .file").change(function() {
        if ($(this).val() != '') {
            var container = $(this).parents(".e6fqus98fp");
            container.clone(true).insertAfter(container);
        }
    });
    
    // Кнопка удаления файла
    $(".e6fqus98fp .delete").click(function(e) {
        e.preventDefault();
        var container = $(this).parents(".e6fqus98fp");
        
        if (container.siblings(".e6fqus98fp").length > 0) {
            $(container).remove();
        } else {
            $(container).find(".file").val('');
        }
    });
    
});
