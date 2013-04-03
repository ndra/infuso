<? 

$user = $log->pdata("user")->title();

<table class='k5513vdl72' >
    <tr>
        <td class='date' >{$log->pdata(datetime)->txt()}</td>                
        
        <td class='user' >{$user}</td>
        
        <td class='object' >
            <a href='{$log->item()->editor()->url()}' >{$log->data(index)}</a>
        </td>
        
        <td>{$log->data(text)}</td>
    </tr>
</table>