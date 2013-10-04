<? 

$url = clone mod::app()->url();

<div>

    $url->query("mode","month");
    <a href='{$url}' >за месяц</a>
    
    echo " ";
    
    $url->query("mode","year");
    <a href='{$url}' >за год</a>
    
</div>