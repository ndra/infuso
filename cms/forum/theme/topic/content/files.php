<? 
<div class='fsbfgut7ai' >
    foreach($post->attachments() as $attachment) {
        $preview = $attachment->pdata("file")->preview(100,100);
        <img src='{$preview}' />
    }
</div>