<?

switch($facade->layout())  {

    default:
        <table class='guc488q7' style='margin-bottom:10px;' >
            <tr>
                <td class='njliyjoc0c-label' >
                    <div class='njliyjoc0c-label' >
                        echo $label;
                    </div>
                </td>
                <td>
                    $template->exec();
                    <div class='error-{$name}'></div>
                </td>
            </tr>
        </table>
        break;
        
    case "checkbox":
        <div style='margin:0px 0px 10px 220px;' >
            $template->exec();
        </div>
        break;
        
    case "none":
        $template->exec();
        if($name)
            <div class='error-{$name}'></div>
        break;
        
}