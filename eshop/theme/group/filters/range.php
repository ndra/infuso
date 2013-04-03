<?

$tag = $p2->tag();

$name = "from_".$p1;
$val = $tag->param($name);
echo "<span>от</span> ";
echo "<input name='$name' class='amn8j5ujfk' style='width:50px;'  filter:tag='{$p2->tag()->id()}' value='$val' /> ";

$name = "to_".$p1;
$val = $tag->param($name);
echo "<span>до</span> ";
echo "<input name='$name' class='amn8j5ujfk' style='width:50px;'  filter:tag='{$p2->tag()->id()}' value='$val' />";