<?

$p = array (
    "type" => "inx.tabs",
    "width" => 600,
    height=>200,
    style=> array(
        padding => 0,
    ),
    "items" => array(),
    side => array(
        array(
            width => 100,
            resizable => true,
            region => right
        )
    )
);

for($i=0;$i<10;$i++)
    $p["items"][] = array(
        "title" => "Таб-".$i,
        type => "inx.tabs",
        closable => $i%2,
        items => array(
            array(
                title => "ololo",
                html => 123
            ),array(
                title => "ololo",
                height => 50,
                html => util_delirium::generate(),
                style=>array(
                    padding=>20
                ),
                vscroll=>true
            ),array(
                title => "ololo",
                style=>array(
                    padding => 1
                ),
                html => 123
            )
            
        )
    );

inx::add($p);

echo "<br/><br/>";

inx::add(array(
	width => 700,
	"type" => "inx.tabs",
	width => 800,
	side => array(
	    array(
	        width => 100,
	        region => right,
	        resizable => true
		)
	),
	items => array(
	    array(
	    	"title" => "текстовое поле",
	    	"type" => "inx.textarea",
		),array(
	    	"title" => "текстовое поле",
	    	"type" => "inx.textfield",
		),array(
		    title => html,
		    html => util_delirium::generate(),
		)
	)
));
