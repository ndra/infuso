<style>
.hqxzkbcdpr td{padding-right:20px;vertical-align:middle;}
.hqxzkbcdpr-title{width:300px;}
</style>
<?

echo "<table class='hqxzkbcdpr' ><tr>";
$preview = $p1->photo()->preview(32,32);
echo "<td><img src='$preview' /></td>";
echo "<td><div class='hqxzkbcdpr-title' >{$p1->title()}</div></td>";
echo "<td styler='text-align:right;' >{$p1->price()} р.</td>";
echo "</tr></table>";

?>