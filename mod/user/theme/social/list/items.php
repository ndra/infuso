<? 

<div style='padding-bottom:20px;' >
    echo "Подключенные профили:";
</div>

<table class='ml6lpfzt' >
    
    foreach(user::active()->socialLinks() as $link) {    
        <tr>
            <td>
                echo $link->data("identity");
            </td>
            <td class='unlink' >
                <div class='button' social:id='{$link->id()}' >&times; удалить</div>
            </td>            
        </tr>
    }
    
</table>
    
<i>Вы можете добавить социальный профиль, выбрав любую сеть ниже:</i>

<div style='padding:10px 0px;' >
    tmp::exec("user:social.login");
</div>