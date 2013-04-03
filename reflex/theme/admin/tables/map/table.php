<? 

$url = mod_action::current()->param("class",get_class($item))->url();
<a class='buxb39x8' href='$url' >

    $class = get_class($item);
    <b>{$class}</b>

    foreach($item->table()->fields() as $field) {
    
        $div = new tmp_helper_html();
        $div->tag("div");  
        $div->addClass("field");
        $div->attr("id",$class."-".$field->name());
        
        if(get_class($field)=="mod_field_link") {
            $div->attr("reflex:target",$field->itemClass()."-".$field->foreignKey());
            $div->addClass("j4vuliikh");
        }
        
        $div->begin();
            echo $field->name();
        $div->end();
    }
    
</a>