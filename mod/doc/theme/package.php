<? 


echo "<h1>Пакет классов: {$p1}</h1>";

foreach(mod::classes() as $class=>$x) {
    
    $r = new reflectionClass($class);
    
    $doc = $r->getDocComment();
    
    if (strpos($doc, "@package {$p1}") !== false) {
        
        $url = mod::action("doc","class",array("class" => $class))->url();
        echo "<h2>" . "<a href='$url'>" . $r->getName() . "</a>" . "</h2>";
        tmp::exec("/doc/class/block",$r->getDocComment());
        echo "<br><br>";
    }
    
    
}
