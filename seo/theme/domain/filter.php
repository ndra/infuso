<? 
tmp::jq();
tmp::singleJS("http://code.jquery.com/ui/1.10.3/jquery-ui.js");
tmp::singleCSS("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");

$current = mod_action::current();
$params = $current->params();
if($params["from_date"]){
    $from = util::date($params["from_date"])->notime()->num();
}

if($params["to_date"]){
    $to = util::date($params["to_date"])->notime()->num();
}


<form method='post'>
<input type='hidden' name='cmd' value='seo:filterByDate'>

<input type='hidden' name='id' value='{$domain->id()}'>От 
<input type='textfield' class='witcdlhmrn' value="$from">
<input type='hidden' name="from_date" value="{$params['from_date']}">До 
<input type='textfield' class='witcdlhmrn' value="$to">
<input type='hidden' name="to_date" value="{$params['to_date']}">
<input type='submit' value="Фильтрануть">
</form>