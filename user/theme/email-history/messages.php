<? 

$user = user::active();
$messages = $user->mailMessages()->addbehaviour("reflex_filter")->page($_GET["page"]);

<div class='ljba1afqg0' >

    <table>
        
        foreach($messages as $message) {
            <tr class='message' >
                <td class='date' >
                    echo $message->sent()->txt();
                </td>
                <td>
                    <h2>{$message->subject()}</h2>
                    <div>
                        echo $message->message();
                    </div>
                </td>
            </tr>
        }
    
    </table>
    
    tmp::exec("/reflex/navigation/pager",$messages);
    
</div>

user::active()->markAllMessagesRead();