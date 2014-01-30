<? 

<div class='tfm1xbo2w3' >

    echo "Последние операции с внутренним счетом:";

    <table>
    foreach(pay_operationLog::all() as $item) {
        <tr>
            <td>
                echo $item->pdata("date")->txt();
            </td>
            <td>
                echo $item->pdata("userId")->title();
            </td>
            <td>
                echo $item->data("comment");
            </td>
            <td>
                echo $item->data("amount");
            </td>
        </tr>
    }
    </table>

</div>

