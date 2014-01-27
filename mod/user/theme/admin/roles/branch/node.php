<? 

tmp::jq();
mod::coreJS();
tmp_lib::jsplumb();
//tmp_lib::highlightjs();

$h = new tmp_helper();
$h->begin();

    $h->addClass("axxvozzes4");
    
    if($operation->data("role")) {
        $h->addClass("axxvozzes4-role");
    }
    
    $parents = array();
    foreach($operation->parentOperations() as $parent) {
        $parents[] = $parent->id();
    }
    $parents = implode(" ",$parents);
    $h->attr("data:parents",$parents);
    
    $h->attr("id","operation-".$operation->id());
    
    $h->tag("div");
    
    echo $operation->code();
    <span class='title' > {$operation->data(title)}</span>
        
    if($php = $operation->data("business-logic")) {
        <pre>
            echo $php;
        </pre>
    }        
        
$h->end();

