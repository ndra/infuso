<? 

mod::coreJs();

$jkeys = base64_encode(json_encode($keys));

<div class='lud3vtwmim' data:id='{$jkeys}' data:type='{$type}' >

    <span>{$title}</span>
    echo " ";
    
    $value = call_user_func_array(array("mod_conf","general"),$keys);
    if($value===null) {
        echo "<i style='color:#ccc;' >не задано</i>";
    } else {
    
        if(is_array($value)) {
            echo "<span>= ".print_r($value)."</span>";
        } else {
            echo "<span>= ".$value."</span>";
        }
    }
    
    <span class='edit' ><img src='/mod/res/img/icons16/edit.png' /></span>
    
    if($value !== null) {
        <span class='delete' ><img src='/mod/res/img/icons16/trash.png' /></span>
    }
    
    <div class='form' style='display:none' >
    
        switch($type) {
            default:
                <input value='{$value}' class='new-value' />       
                break;
            case "yaml":
                <textarea class='new-value yaml' >       
                    echo mod::service("yaml")->write($value);
                </textarea>
                break;
        }
        
        <input type='submit' class='edit-submit' />
    </div>

</div>