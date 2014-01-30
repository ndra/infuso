/**
 * @author Alexey 'Kopleman' Dvourechensky
 * @copyright NDRA Studio (http://www.ndra.ru)
 * @version 0.2
 * @date 2010-09-08
 * @requires jQuery
 **/

$(function() {
    $("<style>.ndra-slideshow-selectedImage{outline:2px solid yellow;}</style>").appendTo("head");
});

if(!window.ndra)
    window.ndra = {};

ndra.slideshow = new function() {

    var eWindow; // Окно, в котором выводится галлерея
    var eLayout; // Белая плашека
    var eFooterSpacer; // Див, растягивающий подвал
    var eRollerContainer;  // Контейнер, в котором катаются маленькие фотографии
    var eRoller; // Блок с маленькими фотографиями, который каается
    var eLoader; // Индикатор загрузки
    var ePreloader; // Промежуточный буфер, пока картинки не загрузились - они в этом блоке
    var eContentContainer; // Контейнер для контента
    var eRuler; // Див для измерения ширины окна с учетом скроллбара

    var rollerX = 0;
    var rollerSpeed = 20;
    var dragMode = 0;
    var dragStartMoseX;
    var dragStartRollerX;
    var mouseX = 0;
    var mouseTime = 0;
    var mouseSpeed = 0;
    var selectedImage = -1;
    var tickIntervalID;
    var iframes = null;
    var params = {};

    var opened = false; // Открыто ли слайдшоу

    var sliderVisible = false; // виден ли слайдер

    var initialParams = {
        hSpacing:100,
        vSpacing:10,
        layoutHSpacing:20,
        layoutVSpacing:20,
        minLayoutWidth:800,
        contentWidth:760,
        previewWidth:100,
        previewHeight:100,
        previewHSpacing:20,
        previewVSpacing:30,
        loaderImg:"/ndra/res/slideshow/loading.gif",
        slider:true,
        minContentHeight:100
    }

    this.init = function() {
        setInterval(ndra.slideshow.tick,1000/60);
        setInterval(ndra.slideshow.updateLayout,1000);
    }

    this.open = function(p) {

        if(!p)
            p = {};
        params = p;

        for(var i in initialParams)
            params[i] = initialParams[i];

        ndra.slideshow.close();

        opened = true;

        iframes = $("iframe:visible");
        iframes.hide();

        $("body,html").addClass("dw92npeij81-body");

        eWindow = $("<div>").addClass("dw92npeij81").appendTo("body");

        eRuler = $("<div>").appendTo(eWindow);

        eLayout = $("<div>").addClass("layout").appendTo(eWindow);

        eFooterSpacer = $("<div>").addClass("footer-spacer").appendTo(eWindow);

        eLoader = $("<img>").attr("src",params.loaderImg).addClass("loader").appendTo(eWindow);

        ePreloader = $("<div>").addClass("preloader").appendTo(eWindow);

        // Контейнер больших фотографий
        eBigImageContainer = $("<div>").appendTo(eLayout);

        // Контейнер контента
        eContentContainer = $("<div>").addClass("content").css({
            width:params.contentWidth
        }).appendTo(eLayout);

        // Предыдущая фотография
        ePrev = $("<div>").addClass("prev").html("&lsaquo;").click(ndra.slideshow.selectPrev)
        .appendTo(eWindow)
        .mouseover(function(){ $(this).stop(true).animate({opacity:1}); })
        .mouseout(function(){ $(this).stop(true).animate({opacity:.1}); });

        // Следующая фотография
        eNext = $("<div>").addClass("next").click(ndra.slideshow.selectNext)
        .appendTo(eWindow)
        .mouseover(function(){ $(this).stop(true).animate({opacity:1}); })
        .mouseout(function(){ $(this).stop(true).animate({opacity:.1}); });

        // Кнопка «закрыть»
        eClose = $("<div>").addClass("close").click(ndra.slideshow.close).appendTo(eWindow);

        // Контейнер роллера
        eRollerContainer = $("<div>").css({
            height:params.previewHeight + params.previewVSpacing * 2,
            position:"fixed",
            bottom:0,
            overflow:"hidden"
        }).addClass("roller-container").appendTo(eWindow);

        // Роллер (див в котором размещаются миниатюры фотографий). Он слайдится внутри контейнера.
        eRoller = $("<div>").css({
            position:"absolute",
            top:params.previewVSpacing,
            whiteSpace:"nowrap"
        }).mousedown(function(e) {
            e.preventDefault();
            e.stopPropagation();
            dragMode = 1;
            rollerSpeed = 0;
            dragStartMouseX = e.pageX;
            dragStartRollerX = rollerX;
        }).appendTo(eRollerContainer);

        $(document).bind("mousemove.ndraSlideshow",function(e) {

            if(dragMode==1) {
                e.preventDefault();
                e.stopPropagation();
            }

            var time = (new Date()).getTime();
            var dt = time-mouseTime;
               var speed = dt>0 ? (e.pageX-mouseX)/dt*1000 : 0;
            var k = .5;
            mouseSpeed = speed*k + mouseSpeed*(1-k);
            mouseTime = time;
            mouseX = e.pageX;
        });

        $(document).bind("mouseup.ndraSlideshow",function(e) {

            if(dragMode==1)
                dragMode = 0;

            if(Math.abs(rollerX-dragStartRollerX)<10) {
                rollerX = dragStartRollerX;
                rollerSpeed = 0;
                var img = $(e.target).parents().andSelf().filter(".h7mgidw1z7cu").first();
                var n = img.data("imgID");
                if(n)
                    ndra.slideshow.selectImage(n);
            }

        });

        $(document).bind("keydown.ndraSlideshow",function(e) {

            switch(e.which) {
                case 27:
                    ndra.slideshow.close();
                    break;

                case 37:
                    ndra.slideshow.selectPrev();
                    break;

                case 39:
                    ndra.slideshow.selectNext();
                    break;
            }

        });



        if(params.loader)
            ndra.slideshow.load(params.loader);
        if(params.data) {
            ndra.slideshow.setData(params.data);
            ndra.slideshow.selectImage(params.select);
        }

        ndra.slideshow.updateLayout();

    }

    /**
     * Закрывает окно слайдшоу
     **/
    this.close = function() {
        $(eWindow).fadeOut("fast",function(){ $(this).remove(); });
        $(document).unbind(".ndraSlideshow");
        $(iframes).show();
        $("body,html").removeClass("dw92npeij81-body");
        opened = false;
    }

    /**
     * Обрабатывает данные
     **/
    this.setData = function(data) {
        for(var i in data) {
            var item = data[i];
            img = $("<div>")
            .addClass("h7mgidw1z7cu")
            .data("imgID",i)
            .data("big",item.big)
            .data("data",item)
            .css({
                display: "inline-block",
                background:"white",
                width:params.previewWidth,
                height:params.previewHeight,
                cursor: "pointer",
                opacity:1,
                marginRight:params.previewHSpacing
            }).appendTo(eRoller);

            var img = $("<img>").css({
                width:params.previewWidth
            }).attr({src:item.small})
            .appendTo(img)
            .mouseover(function(){ $(this).stop(true).animate({opacity:.5},300); })
            .mouseout(function(){ $(this).stop(true).animate({opacity:1},300); });
        }

        if(ndra.slideshow.count()>1) {
            if(params.slider) {
                   sliderVisible = true;
                eRollerContainer.css({
                    display:"block"
                });
            }
            ePrev.show();
            eNext.show();
        } else {
            sliderVisible = false;
            eRollerContainer.css({
                display:"none"
            });
            ePrev.hide();
            eNext.hide();
        }

        eLayout.show();
    }

    /**
     * Загружает галерею
     **/
    this.load = function(loader) {
        ndra.slideshow.open();
        ndra.slideshow.showLoader();
        mod.cmd(loader,function(data) {
            ndra.slideshow.hideLoader();
            ndra.slideshow.setData(data);
            ndra.slideshow.selectImage(params.select);
        });
    }

    /**
     * Показывает индикатор загрузки
     **/
    this.showLoader = function() {
        eLoader.show();
    }

    /**
     * Прячет индикатор загрузки
     **/
    this.hideLoader = function() {
        eLoader.hide();
    }

    /**
     * Возвращает ширину роллера
     **/
    this.rollerWidth = function() {
        var last = eRoller.children().last();
        if(!last.length)
            return 0;
        return last.position().left + last.width();
    }

    /**
     * Возвращаем минимальное положение роллера
     **/
    this.minRollerX = function() {
        return eRollerContainer.width()-ndra.slideshow.rollerWidth() - 100;
    }

    /**
     * Возвращаем минимальное положение роллера
     **/
    this.maxRollerX = function() {
        return 100;
    }

    /**
     * Возвращает количество фотографий на данный момент
     **/
    this.count = function() {
        return eRoller.children().length;
    }

    /**
     * Утстанавливает html-описание
     **/
    this.setHtml = function(html) {
        eContentContainer.html(html);
    }

    /**
     * Перематывает на картинку с номером n
     **/
    this.selectImage = function(n) {

        // Нормируем номер картинки
        n = parseInt(n);
        n = Math.max(n*1 || 0,0);

        // Сохраняем выбранный номер
        selectedImage = n;

        // Выбираем объект маленькой картинки
        var smallImg = eRoller.children().eq(n);

        // Добавляем активной картинке специальный класс
        eRoller.children().removeClass("ndra-slideshow-selectedImage");
        smallImg.addClass("ndra-slideshow-selectedImage");

        // Определяем большую картинку
        var src = smallImg.data("big");
        var data = smallImg.data("data");
        ePreloader.html("");

        var img = $("<img>").css({
            position:"absolute",
            display:"none"
        }).load(function() {

            var width = $(this).width();
            var height = $(this).height();

            $(this).data("width",$(this).width());
            $(this).data("height",$(this).height());
            $(this).fadeIn("fast");

            eBigImageContainer.html("");

            if(data.html)
                eContentContainer.html(data.html);

            $(this).appendTo(eBigImageContainer);

            ndra.slideshow.hideLoader();
            ndra.slideshow.updateLayout();


        }).appendTo(ePreloader)
        .attr("src",src);

        ndra.slideshow.showLoader();

        dragMode = 2;
    }

    this.bind = function(selector,params) {

        if(!params)
            params = {};

        var items = $(selector);
        items.click(function(e) {
            var href = $(this).attr("href");
            e.preventDefault();
            if(!params.loader) {
                var data = [];
                var selected = 0;
                $(selector).each(function(n){
                    var big = $(this).attr("href");
                    var html = $(this).attr("title");
                    if(big==href)
                        selected = n;
                    data.push({
                        small:$(this).find("img").attr("src"),
                        big:big,
                        html:html
                    });
                });
                params.data = data;
                params.select = selected;
            }
            ndra.slideshow.open(params);
        });
    }

    /**
     * Выбрать предыдущую фотографию
     **/
    this.selectNext = function() {
        var n = selectedImage*1+1;
        if(n>=ndra.slideshow.count())
            n = 0;
        ndra.slideshow.selectImage(n);
    }

    /**
     * Выбрать следующую фотографию
     **/
    this.selectPrev = function() {
        var n = selectedImage*1-1;
        if(n < 0)
            n =  ndra.slideshow.count()-1;
        ndra.slideshow.selectImage(n);
    }

    /**
     * Расставляет элементы управления по местам
     **/
    this.updateLayout = function() {

        if(!opened)
            return;

        // Позиционируем загрузчик

        eLoader.css({
            left:($(window).width() - eLoader.width())/2
        });

        // Определяем высоту слайдера
        var rollerHeight = sliderVisible ? (params.previewHeight + params.previewVSpacing*2) : 0;

        // Есть ли контент?
        var isContent = !!eContentContainer.html().length;

        // Сколько должно быть видно контента без прокрутки
        var mh = Math.min(eContentContainer.height(),params.minContentHeight);
        var minContentHeight = isContent ? (mh + params.layoutVSpacing) : 0;

        // Ширина и высота "Окна"
        var windowWidth = eRuler.width();
        var windowHeight = $(window).height();

        // Максимальная ширина большой фотографии
        var maxWidth = windowWidth - params.hSpacing * 2 - params.layoutHSpacing * 2;
        // Максимальная высота большой фотографии
        var maxHeight = windowHeight - params.vSpacing * 2  - params.layoutVSpacing * 2 - rollerHeight - minContentHeight;
        if(maxHeight<200)
            maxHeight = 200;
        var width = 0;
        var height = 0;

        // Позиционируем большуб фотографию

        eBigImageContainer.children().each(function() {

            var k1 = maxWidth / maxHeight;
            var k2 = $(this).data("width") / $(this).data("height");

            if(k1>k2) {
                $(this).css({
                    width:"auto",
                    height:maxHeight
                });
            } else {
                $(this).css({
                    height:"auto",
                    width:maxWidth
                });
            }

            width = Math.max(width,$(this).width());
            height = Math.max(height,$(this).height());

        });

        eBigImageContainer.css({
            height:height + params.layoutVSpacing * 2
        });

        // Позиционируем белый листик

        // Расчитываем ширину листика
        var layoutWidth = width + params.layoutHSpacing * 2;
        layoutWidth = Math.max(layoutWidth,params.minLayoutWidth);

        eLayout.css({
            width:layoutWidth,
        });

        eLayout.css({
            top:params.vSpacing,
            paddingBottom: isContent ? params.layoutVSpacing : 0,
            left:($(window).width() - eLayout.width()) / 2
        });

        eFooterSpacer.css({
            height:1,
            overflow:"hidden",
            left:0,
            top:eLayout.outerHeight() + (isContent ? (rollerHeight+params.vSpacing * 2) : 0) - 1
        }).html("&nbsp;");

        eBigImageContainer.children().each(function() {
            $(this).css({
                left:(layoutWidth - $(this).width())/2,
                top:params.layoutVSpacing
            });
        });

        // Позиционируем кнопки вперед - назад

        var sideWidth = (windowWidth - layoutWidth)/2;
        var sideHeight = $(window).height() - rollerHeight;

        eNext.css({
            left:sideWidth + layoutWidth,
            width:sideWidth,
            height:sideHeight
        });

        ePrev.css({
            width:sideWidth,
            height:sideHeight
        });

        // Позиционируем загрузчик

        eLoader.css({
            left:(windowWidth - eLoader.width())/2,
            top:(windowHeight - eLoader.height())/2
        });

        eRollerContainer.width(windowWidth);

    }

    /**
     * Эта функция вызывается 60 раз в секунду для создания плавной анимации
     **/
    this.tick = function() {

        if(!opened)
            return;

        if(!ndra.slideshow.count())
            return;

        switch(dragMode) {
            case 0:
            case 2:

                if(dragMode==2) {
                    var img = eRoller.children().eq(selectedImage);
                    var imgX = img.offset().left + img.width()/2
                    var dx = imgX - eRollerContainer.width()/2;
                    rollerSpeed= -dx/10;
                }

                if(ndra.slideshow.rollerWidth() > eRollerContainer.width()) {
                    // Выход за левую границу
                    if(rollerX>=ndra.slideshow.maxRollerX() && rollerSpeed>=-10) {
                        rollerSpeed = 0;
                        rollerX =ndra.slideshow.maxRollerX()*.3 + rollerX*.7;

                        if(dragMode==2) {
                            dragMode = 0;
                            rollerX = ndra.slideshow.maxRollerX();
                            rollerSpeed = 0;
                        }
                    }
                    // Выход за правую границу
                    if(rollerX<=ndra.slideshow.minRollerX() && rollerSpeed<=10) {
                        rollerSpeed = 0;
                        rollerX = ndra.slideshow.minRollerX()*.3 + rollerX*.7;

                        if(dragMode==2) {
                            rollerSpeed = 0;
                            rollerX = ndra.slideshow.minRollerX();
                            dragMode = 0;
                        }
                    }
                } else {
                    var x = (eRollerContainer.width() - ndra.slideshow.rollerWidth())/2;
                    rollerX = x*.3 + rollerX*.7;
                    rollerSpeed = 0;
                    if(dragMode==2) dragMode = 0;
                }

                rollerSpeed*=.97;
                if(Math.abs(rollerSpeed)<1) rollerSpeed = 0;
                rollerX+=rollerSpeed;
                break;
            case 1:
                if(Math.abs(mouseX - dragStartMouseX)>10) {
                    rollerX = (mouseX - dragStartMouseX) + dragStartRollerX;
                    rollerSpeed = mouseSpeed/60;
                }
                break;

        }
        eRoller.css({left:rollerX});
    }

}();

ndra.slideshow.init();
