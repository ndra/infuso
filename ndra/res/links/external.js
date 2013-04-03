//Открываем внешние ссылки в новом окне.
//Petr Grishin <petr.grishin@grishini.ru>
$(function(){
    $("a").each(function() {
        var a = new RegExp('^http(s)?:\/\/(www\.)?(?!'+ window.location.host + ')');
        
        if(a.test(this.href)) {
            $(this).click(function(event) {
                event.preventDefault();
                event.stopPropagation();
                window.open(this.href, '_blank');
            });
        }
    });
}); 