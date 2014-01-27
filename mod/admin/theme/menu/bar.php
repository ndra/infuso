<?

<table id='raub2v07e-header'>
    <tr>    
        <td id='raub2v07e-title' >
            $title = tmp::param("title");
            if(!tmp::param("back-end")) {
                $item = tmp::obj();
                if($item->exists() && $item->editor()->beforeView())
                    $title.= "<a href='{$item->editUrl()}'>редактировать этот раздел</a>";
            }        
            echo "$_SERVER[HTTP_HOST] | $title";
        </td>
        
        // Добавляем в шапку стандартные блоки
        tmp::add("admin-header","info");
        tmp::add("admin-header","log");
        
        foreach(tmp::block("admin-header")->templates() as $block) {
            <td id='raub2v07e-item' >
                $block->exec();
            </td>
        }
                
        <td style='font-weight:bold;'>
            $url = mod::action("mod_about")->url();
            echo "Работает на <a href='$url' style='color:red;' >infuso</a>";
        </td>    
    </tr>
</table>
