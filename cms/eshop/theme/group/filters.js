$(function() {

    $(".w6orymz2tv-form").submit(function(e) {
        e.preventDefault();
        var data = {};
        var items = $(this).find(":input:visible");
        items.each(function() {
            var name = $(this).prop("name");
            switch(name.split("_")[0]) {
                case "eq":
                    if($(this).attr("type")=="checkbox" && !$(this).prop("checked")) return;
                    if($(this).attr("type")=="radio" && !$(this).prop("checked")) return;
                    if($(this).val()=="*") return;
                    var l = data[$(this).prop("name")];
                    var val = (l?l+",":"")+$(this).val();
                    data[$(this).prop("name")] = val;
                    break;
                case "from":
                case "to":
                case "like":
                    var val = $.trim($(this).val());
                    if(val) data[$(this).prop("name")] = val;
                    break;
            }
        })

        var action = $(this).attr("action") || "";
        data = action + "?"+$.param(data);
        window.location.href = data;

    });
    
})