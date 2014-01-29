<? 

<div class='ho8gst45s' >
    <span class='close' onclick='\$(this).parents(".ho8gst45s").remove()' >&times;&nbsp;Скрыть</span>
    
    <table><tr>
    
    if($item->exists()) {
        <td style='padding-right:20px;' >
        <a target='_new' href='{$item->editor()->url()}'>Редактировать</a><br/><br/>
    
        <div>
            echo "Последние изменения:<br/>";
            foreach($item->getLog()->limit(3) as $log) {
                echo "<span style='margin-right:10px;' >".$log->pdata("datetime")->left()."</span>";
                echo "<span style='margin-right:10px;' >".$log->user()->title()."</span>";
                echo $log->data("text");
                echo "<br/>";
            }
            </div>
        </td>
    }
    
    </tr></table>

</div>