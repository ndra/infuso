// @include inx.chart

inx.ns("inx.mod.user.editor").report = inx.chart.extend({

    constructor:function(p) {
        p.cols = ["actions","objects"];        
        p.loader = {cmd:"user:manager:getReport",userID:p.userID};
        p.side = [{
            html:"Отчет ежедневной активности пользователя. Синим показано число изменений (сколько раз пользователь нажал на кнопку «Сохранить», «Удалить» и т.п.) Красным показано число измененных объектов.",
            padding:10,
            region:"top",
            background:"#ededed"
        }]
        this.base(p);
    }

})