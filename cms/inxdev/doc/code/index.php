<?

$x = "************************************************************
djuy6l*61djuy6l*61djuy6l*61djuy6l*61djuy6l*61djuy6l*61djuy6l";

inx::add(array(
    "type" => "inx.code",
    "height" => 300,
    "width" => 900,
    "lang" => "php",
    value => $x.file::get("mod/class/mod.php")->data(),
    side => array(
        array(
            resizable => true,
            width => 100,
            region => "right"
        ),
        array(
            resizable => true,
            height => 50,
            region => "top"
        )
    )
));
