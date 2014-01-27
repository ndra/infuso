<?

$items = eshop_order::myOrders();
$items->limit(10);

<table class='adij3jbap' >
    foreach($items as $order) {
        <tr>
            <td>
                <div class='adij3jbap-title' ><a href='{$order->url()}' >{$order->title()}</a></div>
                foreach($order->items() as $item) {
                    <a href='{$item->url()}' style='padding-right:20px;' >{$item->title()}</a>
                    echo " ";
                }    
            </td>            
            <td>
                echo $order->status()->title();
            </td>
        
        </tr>
    }
</table>

$items->addBehaviour("reflex_filter");
tmp::exec("reflex:navigation.pager",$items);