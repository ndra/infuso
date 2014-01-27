<? 

admin::header("таблицы");
    
    <form class='mf1wbdgwpk' >
        $input = new tmp_helper_html();
        $input->tag("input");
        $input->attr("name","search");
        $input->attr("placeholder","Префикс таблиц");
        $input->attr("value",$search);
        $input->exec();
        <input type='submit' value='Показать' />
    </form>

    <div class='xowcqr13p' >
    
        if($class) {
        
            tmp::exec("table",array(
                "item" => reflex::virtual($class),
            ));
        
        } else {
        
            tmp::exec("map");
            
        }
        
    </div>
    
admin::footer();