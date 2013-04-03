<?

foreach(reflex_mysql::getDiffVariables() as $key=>$val) {
    if($val) {
        echo $key.": ".$val;
        echo "<br/>";
    }
}