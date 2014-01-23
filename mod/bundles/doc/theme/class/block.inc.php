<?

foreach(explode("\n",$p1) as $line) {
    $line = trim($line);
    if($line=="/**")
        continue;
    if($line=="**/")
        continue;
        
    $line = preg_replace("/^\*\s*/","",$line);
    
    if(preg_match("/^(\@([a-z]+))(.*)$/",$line,$matches)) {
        $tag = $matches[2];
        if(in_array($tag,array("return","param","var","author","package","todo","humor"))) {
            tmp::exec($tag,$matches[3]);
        }
    } else {
        echo $line."<br/>";
    }        
    
}

?>