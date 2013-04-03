<?

$p = array (
    "type" => "inx.tabs",
    "width" => 600, 
    tbar => array(
        items => array(
            text => 123,
            icon => plus,
            onclick => " var name = 'tab-'+Math.round(Math.random()*3); this.owner().owner().cmd('add',{title:name,name:name,closable:true}) "
        )
    ),
    side => array(
        array(
            region => right,
            width => 20,
            resizable => true
        )
    )
);

inx::add($p);