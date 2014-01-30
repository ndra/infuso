<?

if($p1->vendor()->exists())
    echo "<div>Производитель: {$p1->vendor()->title()}</div>";
echo $p1->pdata("description");

?>