<?

echo "<h1>Todo</h1>";

foreach(mod::classes() as $class=>$x) {
    
    $r = new reflectionClass($class);
    
    foreach($r->getMethods() as $method) {
        
        $doc = $method->getDocComment();
        
        if (strpos($doc, "@todo") !== false) {
        
            $url = mod::action("doc","class",array("class" => $class))->url();
            
            echo "<h2>" . "<a href='$url'>" . $r->getName() . "</a>" . "::" . $method->getName() . "</h2>";
            
            tmp::exec("/doc/class/block",$doc);
            
            echo "<br />";
            echo "<br />";
        }
        
    }
    
    
}
