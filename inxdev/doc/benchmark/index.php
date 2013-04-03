<?

$p = array(
    "width" => 800,
    height => 300,
    style => array(
        "spacing" => 3,
        padding => 15,
        "background" => "#ededed",
        vscroll => true,
        "padding" => 1,
    ),
    autoHeight => true,
    "type" => "inx.panel",
    "items" => array(),
    "side" => array(
        array (
            "width" => 200,
            "region" => "right",
            "resizable" => true
        )
    )
);

for($i=0;$i<100;$i++)
    $p["items"][] = array(
        "type" => "inx.panel",
        "html" => util_delirium::generate(70,50),
        "width" => "parent",
        "height" => "content",
    );
    
tmp::js("/inxdev/res/benchmark.js");
tmp::script("$(function(){ inx.benchmark(function(){ inx('inx-1').cmd('width',200+Math.random()*500); }); })");
    
inx::add($p);
