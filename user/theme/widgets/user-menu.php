<? 

<div class='gelqz8dj3s' >

    foreach($items as $item) {
    
        $class = $item->url()==mod_url::current()->path() ? "active" : "";
        <span class='$class' >
            <a href='{$item->url()}' >{$item->title()}</a>
            if($sup=$item->param("sup"))
                <span class='sup' >{$item->param(sup)}</span>
        </span>
    }

</div>