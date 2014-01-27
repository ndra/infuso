<? 
if ($attachments->count() > 0){



<div class="b-remainFiles-c34dv9u4ud">
    
    foreach ($attachments as $file) {
        if ($file->typeImg()) {
            <div class="img attachment-{$file->id()}">
                <table>
                    <tr>
                        <td>
                            <img src="{$file->pdata('file')->preview(150,150)->resize()}" width="150" class=''/>
                        </td>
                        <td>
                            <a href='#' class='delete' attach:id='{$file->id()}'>x</a>
                                
                        </td>
                    </tr>
                    <tr>
                        <td colpan='2'>
                            <p>{$file->title()}</p>
                        </td>
                    </tr>
                 </table>   
            </div>
        } else {
            <div class="file attachment-{$file->id()}">
                <a href="{$file->data('file')}" target="_blank" >{$file->data('file')}</a>&nbsp;
                <a href='#' class='delete' attach:id='{$file->id()}'>x</a>
            </div>
        }
    }
    <input type='hidden' name="deletedattachments">
   
</div>


}