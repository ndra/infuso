<? 

foreach(mod::classes() as $class=>$x) {
    $url = mod::action("doc","class",array(
        "class" => $class,
    ))->url();
    echo "<a href='$url' >";
    echo $class."<br>";
    echo "</a>";
}








