<?

echo "При изменении текстового значения поля вызывается событие change, даже если фокус остается в поле.";
echo "Значение, переданное в конструкторе не должно приводить к вызову события change";

inx::add(array(
	"type" => "inx.textfield",
	"onchange" => "inx.msg(arguments[0])",
    "value" => "Привет"
));

inx::add(array(
	"type" => "inx.textfield",
    "height" => 100,
    "value" => "Привет"
));

