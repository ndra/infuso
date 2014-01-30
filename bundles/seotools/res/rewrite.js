$(function() {

	$(document).keydown(function(event) {
	
	    // При нажатии ctrl+r показываем окно с предложением замены
	    if(event.which==82 && event.ctrlKey) {
	    
	        // Отключаем событие по умолчанию - перезагрузку страницы
	    	event.preventDefault();
	    	
			// Выделенный текст
	    	var original = window.getSelection()+"";
	    	
	    	var replacement = prompt("Заменить текст «"+original+"» на следующий:");
	    	
	    	if(replacement!==null) {
	    	    mod.cmd({
	    	        cmd:"seotools_rewrite_controller:save",
	    	        original:original,
	    	        replacement:replacement
				},function() {
				    location.reload();
				});
	    	}
	    	
	    	
	    }
	    
	});

});
