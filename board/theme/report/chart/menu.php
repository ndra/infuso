<?

$menu = array(
    "Разбить столбцы:",
    array (
        "label" => "Проекты",
        "params" => array(
            "split" => "project",
        )
    ), array (
        "label" => "Пользователи",
        "params" => array(
            "split" => "user",
        )
    ),
    
    "","","","",
    "Ввод времени:",
    
    array (
        "label" => "Авто",
        "params" => array(
            "mode" => "auto",
        )
    ), array (
        "label" => "Вручную",
        "params" => array(
            "mode" => "manual",
        )
    ),
);

<div class='kirfl8wl3u'>

    foreach($menu as $item) {
        <span>
        
            if(is_string($item)) {
            
                echo $item;
            
            } else {
        
                $html = $item["label"];
                
                $active = true;
                foreach($item["params"] as $key=>$val) {
                    if($params[$key] != $val) {
                        $active = false;
                    }
                }
                
                if($active) {
                    <b>{$html}</b>
                } else {
                    $url = mod_action::current()->params($item["params"]);
                    <a href='{$url}' >{$html}</a>
                }
            
            }
            
        </span>
    }

</div>