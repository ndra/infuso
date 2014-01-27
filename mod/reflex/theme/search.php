<?
tmp::jq();
mod::coreJS();
tmp::js("/reflex/res/suggest.js");

echo "<form action='/reflex/search/' style='white-space:nowrap;' >";
echo "<input id='reflex-search' name='q' />";
echo "<input type='submit' value='Найти' />";
echo "</form>";