// @link_with_parent

/**
 * Возвращает адрес картинки
 * Вартианты:
 * plus => .../img/plus.gif
 * %img%/myfilder/plus.png => .../img/myfolder/plus.png
 **/
inx.img = function(name) {

    if(!name) {
        return false;
    }
    
    if((name+"").match(/^[\w-_]+$/)) {
        return inx.path("%res%/img/icons16/"+name+".gif");
    }
        
    return name;
}
