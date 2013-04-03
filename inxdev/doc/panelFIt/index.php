<?

$p = array(     
    "width" => 400,
    autoHeight => true,
    tbar => array(
        array(text => "This is a button",),
        array(text => "This is a button",),
        array(text => "button",),
        array(text => "This is a button",),
        array(text => "button",),
        array(text => "button",),
        array(text => "This is a button",),
        array(text => "This is a button",),
    ),
   
    "layout" => "inx.layout.fit",
    
    style=>array(
        padding => 20
    ),
    "type" => "inx.panel",
    "items" => array(
        array(            
            html => util_delirium::generate(),
        ),
    ),
    side => array(
        array(
            "id" => "left",
            region => left,
            width => 10,
            style=> array(
                background => "#ededed"
            ),
            resizable => true,
        ),
        array(
            region => right,
            width => 10,
            style=> array(
                background => "yellow"
            ),
            resizable => true,
        ),array(
            region => bottom,
            height => 100,
            resizable => true,
            html=>util_delirium::generate(),
            vscroll => true,
        )
    )
);    
   
inx::add($p);

