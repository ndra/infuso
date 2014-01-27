<?


inx::add(array(
    "type" => "inx.list",
    "width" => 500,
    "height" => 200,
    "loader" => array(
        "cmd" => "inxdev:example:listLoader"
    ),
    "side" => array(
        array (
            "width" => 200,
            "region" => "left",
            "resizable" => true,
            "html" => util_delirium::generate(),
        )
    ),
    tbar => array(
        array(
            icon => "refresh",
			onclick => "this.owner().owner().cmd('load')"
		)
	)
));
