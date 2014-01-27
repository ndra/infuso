<?php

<div style="width:180px; padding:20px; border:1px solid #333;">
    <h1 style="margin:0;">Pay2Pay</h1>
    <form id="form-hvli9ghhgo" action="https://merchant.pay2pay.com/?page=init" method="post">
        <input type="hidden" name="xml" value="{$xml_encode}" />
        <input type="hidden" name="sign" value="{$sign_encode}" />
    </form>
    <p>Перенаправление...</p>
</div>

<script>document.forms["form-hvli9ghhgo"].submit();;</script>
