<? 

$key = "node".$operation->id();
if(tmp::param($key)) {
    return;
}
tmp::param($key,true);

<table class='hbmu9r92ay' >

    <tr>
        <td>
            tmp::exec("node");
        </td>
        <td>
            foreach($operation->suboperations() as $op) {
                tmp::exec("../branch",array(
                    "operation" => $op,
                ));
            }
        </td>
    </tr>
    
</table>