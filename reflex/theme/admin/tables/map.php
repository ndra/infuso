<? 

tmp_lib::jsplumb();

foreach(reflex::classes() as $class) {

    if(!$search || preg_match("/^$search/",$class)) {

        $item = reflex::virtual($class);
        
        tmp::exec("table",array(
            "item" => $item,
        ));
        
    }

}