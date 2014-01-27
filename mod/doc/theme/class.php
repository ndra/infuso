<?

tmp::Jq();

$r = new reflectionClass($class);

<div class='control-panel-pjy7g7ij34'>
    <a href='#' class='show-inherited'>Показать наследованные методы</a>
</div>

<div style='padding:0px 0px 70px 0px;' >
    <h1>Класс {$r->getName()}</h1>
    tmp::exec("block", $r->getDocComment());
</div>


foreach($r->getMethods() as $method) {
    
    // Наследованные методы
    $inherited = false;
    if ($method->getDeclaringClass()->getName() != $r->getName()) {
        $inherited = true;
    }
    
    echo "<div style='padding:0px 0px 30px 0px;' class='" . ($inherited ? "inherited-pjy7g7ij34" : "") . "' >";
    echo "<h2>".$r->getName()."::".$method->getName();
    
    if ($inherited) {
        echo " <span>(класс ".$method->getDeclaringClass()->getName().")</span>";
    }
    
    echo "</h2>";
    
    echo tmp::get("block")->param("p1", $method->getDocComment())->rexec();    
    echo "</div>";
    
}