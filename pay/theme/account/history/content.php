<? 

$user = user::active();
<h1>Операции с внутреним счетом {$user->title()} #{$user->id()}</h1>

<table class='w22e7mla9n' >

    <thead>
        <tr>
            <td>Номер операции</td>
            <td>Дата</td>
            <td>Сумма</td>
            <td>Комментарий</td>
        </tr>
    </thead>

    foreach($items as $item) {
    
        $class = $item->amount()>0 ? "incoming" : "expenditure";
    
        <tr class='$class' >
            <td>
                echo $item->id();
            </td>
            <td>
                echo $item->pdata("date")->num();
            </td>
            <td class='amount' >
                echo $item->amount();
                echo "&nbsp;";
                echo mod::field("currency")->value($user->accountCurrency())->code();
            </td>
            <td>
                echo $item->comment();
            </td>
        </tr>
    }
</table>