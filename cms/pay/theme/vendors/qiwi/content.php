<?

<form id="form" method="post">
    <input type="hidden" name="cmd" value="pay:vendors:qiwi:create" />
    <input type="hidden" name="id" value="{$id}" />
    <div><label>Введите номер QIWI кошелька для выставления счета: </label><input type="text" name="number" value="{$number}" /></div>
    
    if (tmp::param("pay-vendors-qiwi-error")) {
        <div class="error" style="padding:10px; background:#F2DEDE; border:1px solid #B94A48; color:#B94A48;">
            echo tmp::param("pay-vendors-qiwi-error");
        </div>
    }
    
    <div><input type="submit" value="Выставить счет" /></div>
</form>
