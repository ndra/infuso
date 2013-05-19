<?

echo "Команда replace";

inx::add(array(
    "type" => "inx.panel",
    "width" => 400,
    "items" => array(
        array(
            "html" => "panel-1"
        ),array(
            "html" => "panel-2"
        ),array(
            "html" => "panel-3"
        ),
    ),
    "bbar" => array(
        array(
            "text" => "replace",
            "onclick" => "var cmp = this.owner().owner().items().get(1); this.owner().owner().cmd('replace',cmp.id(),{html:Math.random(),type:'inx.panel'}); "
        )
    )
));



