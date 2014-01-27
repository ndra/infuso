<?

$p = array(
    "width" => 800,
    padding => 10,
    height => 200,
    "type" => "inx.panel",
    "items" => array(),
    "side" => array(
        array (
            "id" => "side",
            "width" => 200,
            "region" => "left",
            "resizable" => true,
            "html" => util_delirium::generate(),
        )
    )
);

for($i=0;$i<20;$i++)
    $p["items"][] = array(
        "type" => "inx.panel",
        "html" => util_delirium::generate(null,50),
        "height" => 20,
        "autoHeight" => false,
    );
    
   
inx::add($p);

