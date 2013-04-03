<?

inx::add(array(
    "type" => "inx.mod.inxdev.example.dialog",
    width => 400,
    "side" => array(
        array (
            "width" => 200,
            "region" => "left",
            "resizable" => true,
            "html" => util_delirium::generate(),
        )
    ),
    "height" => 200,
));