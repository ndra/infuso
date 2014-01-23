<? 

$block = tmp_block::get("center");
$block->prepend(new user_menu());
tmp::exec("tmp:layout");