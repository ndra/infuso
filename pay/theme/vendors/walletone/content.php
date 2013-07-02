<?
$data = $invoice->driver("walletone")->generatePaymentData();
<div class="info-tnkznv4uiq">Подождите, идет переход на платежный мерчант</div>
<form method="post" class="walletonePayForm-ingp023eg0" action="https://merchant.w1.ru/checkout/default.aspx" accept-charset="UTF-8" style="display:none;">
foreach($data as $key => $val)
{
    if (is_array($val))
    foreach($val as $value)
    {
    echo "$key: <input type=\"text\" name=\"$key\" value=\"$value\"/><br>";
    }
    else        
    echo "$key: <input type=\"text\" name=\"$key\" value=\"$val\"/><br>";
}


  <input type="submit"/>
</form>