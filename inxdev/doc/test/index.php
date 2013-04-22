<?

$p = array (
	"type" => "inx.form",
    width => 400,
    items => array(
        array(
            type => "inx.checkbox",
            label => "Тэст" ,
            onchange => "this.owner().items().eq('name','panel').cmd(Math.random()<.5==0 ? 'show' : 'hide' )"
        ),
        array(
            name => "panel",
            type => "inx.panel",
            label =>433434,
            html => 12121,
        ),
    )


);

inx::add($p);
