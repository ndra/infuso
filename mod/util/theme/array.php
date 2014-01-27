<?

switch(gettype($data)) {

    case "array":
        foreach($data as $key=>$val) {
            <table style='border-left:1px solid blue;' >
                <tr>
                    <td>
                        <div style='min-width:50px;' >{$key} => </div>
                    </td>
                    <td>                        
                        tmp::exec("../array",array(
                            "data" => $val
                        ));
                    </td>
                </tr>    
            </table>
        }
        break;
        
    default:
        echo util::str($data)->ellipsis(300);
        break;

}