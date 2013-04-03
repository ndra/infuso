$(function() {

    var loadRegions = function(region) {
        var countryID = region.data("countryID");
        mod.cmd({
            cmd:"geo_controller:regions",
            countryID:countryID
        },function(regions) {
            region.html("");
            $("<option>").text("Выберите регион").appendTo(region);
            for(var i in regions) {
                $("<option>").text(regions[i]).attr("value",i).appendTo(region);
            }
        });
    }
    
    var loadCities = function(city) {
        var regionID = city.data("regionID");
        mod.cmd({
            cmd:"geo_controller:cities",
            regionID:regionID
        },function(cities) {
            city.html("");
            $("<option>").text("Выберите город").appendTo(city);
            for(var i in cities) {                
                $("<option>").text(cities[i]).attr("value",i).appendTo(city);
            }
            city.parents(".el2rmjirr-block").first().animate({opacity:1},"fast");
        });
    }

    $(".el2rmjirr-country").change(function() {    
        var container = $(this).parents(".el2rmjirr-container").first();
        var region = container.find(".el2rmjirr-region");
        var city = container.find(".el2rmjirr-city");
        var input = container.find("input[name='country']");
        var returnData = container.attr("geo:returnData");
        region.data("countryID",$(this).val());
        city.parents(".el2rmjirr-block").first().animate({opacity:0},"fast");
        if(returnData=="title"){ //проверяем какие данные нам надо вернуть
           var sendData = $(this).find("option:selected").text(); 
        }else{
           var sendData = $(this).val();  
        }
        input.val(sendData); //записываем в скрытый инпут
        loadRegions(region);        
    })
    
    $(".el2rmjirr-region").change(function() {    
        var container = $(this).parents(".el2rmjirr-container").first();
        var city = container.find(".el2rmjirr-city");
        var input = container.find("input[name='region']");
        var returnData = container.attr("geo:returnData");
        city.data("regionID",$(this).val());
        if(returnData=="title"){//проверяем какие данные нам надо вернуть
           var sendData = $(this).find("option:selected").text(); 
        }else{
           var sendData = $(this).val();  
        }
        input.val(sendData);//записываем в скрытый инпут
        loadCities(city);
    })
    
    $(".el2rmjirr-city").change(function() {
        var container = $(this).parents(".el2rmjirr-container").first();
        var input = container.find("input[name='city']");
        var returnData = container.attr("geo:returnData");
        if(returnData=="title"){ //проверяем какие данные нам надо вернуть
           var sendData = $(this).find("option:selected").text(); 
        }else{
           var sendData = $(this).val();  
        }
        input.val(sendData);//записываем в скрытый инпут
    })   
    
});