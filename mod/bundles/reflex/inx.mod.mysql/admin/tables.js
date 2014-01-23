// @link_with_parent
// @include inx.list

inx.mod.mysql.admin.tables = inx.list.extend({

    constructor:function(p) {
        p.loader = {cmd:"mysql:admin:getTables"};
        p.tbar = [
            {text:"Удалить лишние таблицы",onclick:[this.id(),"removeUnnecessary"]}
        ];
        this.base(p);
    },
    
    cmd_removeUnnecessary:function() {
        n = Math.round(Math.random()*100000)+1000;
        if(window.prompt("Введите "+n)!=n) {
            inx.msg("Число введено неверно",1);
            return;
        }
        this.call({cmd:"mysql:admin:removeUnnecessary"},[this.id(),"load"]);
    }

});
