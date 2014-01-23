<? 

<table class='i96nzebywb' >
    
    foreach(user::active()->subscriptions() as $item) {    
        <tr>
            <td>
                echo $item->data("title");
            </td>
            <td class='unsubscribe' >
                <div class='button' subscription:id='{$item->id()}' >&times; удалить</div>
            </td>            
        </tr>
    }
    
</table>  
