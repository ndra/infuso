<? 

<div class='nk3owugb6'>

    <div class='title-block' >
        
        <p>
            <i>Номер счета:</i> {$invoice->id()}
        </p>
    
        <h1>{$invoice->statusText()}</h1>        
        if($invoice->errorText())
            <p>{$invoice->errorText()}</p>
    </div>
    
    <p>
        <i>Назначение платежа: </i> {$invoice->details()}
    </p>
    <p>
        <i>Сумма:</i> {$invoice->sum()} {$invoice->currencyName()}
    </p>
    <p>
        <i>Выставлен:</i> {$invoice->pdata(date)->txt()}
    </p>
    
    if($invoice->paid()) {
        <p>
            <i>Оплачен:</i> {$invoice->pdata(date_incoming)->txt()}
        </p>
        <p>
            <i>Платежная система:</i> {$invoice->data(driver)}
        </p>
    }    
    
    if($r = $invoice->redirectURL()) {
        <div class='buttons' >
            tmp::exec("/pay/button",array(
                "text" => "Далее",
                "href" => $r,
            ));
        </div>
    }

</div>