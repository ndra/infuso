<? 

admin::header();

<div style='padding:40px;' >

    tmp::exec("../menu");
    
    $confSet = mod_conf::general();
    
    $confDescr = array();
    
    foreach(mod::service("classmap")->getClassesExtends("mod_component") as $class) {    
        $confDescr = array_merge_recursive($confDescr,call_user_func(array($class,"confDescription")));        
    }
    
    $renderConf = function($confDescr,$keys = array()) use (&$renderConf) {
        foreach($confDescr as $key => $item) {   
        
            <div style='padding:5px;padding-left:20px;' >
        
                if(is_array($item)) {            
                    <div><b>{$key}</b></div>
                    $keys2 = $keys;
                    $keys2[] = $key;
                    $renderConf($item,$keys2);            
                }    
                
                if(is_scalar($item)) {
                    <div>
                    
                        $keys2 = $keys;
                        $keys2[] = $key;
                        
                        preg_match("/^(\[(.*)\])?(.*)$/",$item,$matches);
                        $type = $matches[2];
                        $title = $matches[3];
                        
                        tmp::exec("field",array(
                            "keys" => $keys2,
                            "title" => $title,                        
                            "type" => $type,                        
                        ));
                    
                    </div>
                }    
            
            </div>
        }
    };
    
    $renderConf($confDescr);

</div>

admin::footer();