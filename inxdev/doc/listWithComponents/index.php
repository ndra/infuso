<?


inx::add(array(
    "type" => "inx.list",
    "width" => 800,
    "height" => 300,
    "loader" => array(
        "cmd" => "inxdev:example:listLoaderWithComponents"
    ),
    "side" => array(
        array (
            "width" => 200,
            "region" => "left",
            "resizable" => true,
            "html" => util_delirium::generate(),
        )
    )
));
