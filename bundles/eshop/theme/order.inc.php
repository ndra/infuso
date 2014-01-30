<?

$order = $p1;
$action = $p2["action"];

if(!$order->editable())
    $action = "info";
    
switch($action) {
    default:
        tmp::add("center","eshop:order.content",$order);
        break;
    case "form":
        tmp::add("center","eshop:order.form",$order);
        break;
    case "info":
        tmp::add("center","eshop:order.info",$order);
        break;
}

tmp::exec("eshop:layout");