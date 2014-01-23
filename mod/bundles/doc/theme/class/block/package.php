<?

$p1 = trim($p1);

<div>
<b>Группа классов </b>
$url = mod::action("doc","package",array("package" => $p1))->url();
<a href='$url'>{$p1}</a>
</div>