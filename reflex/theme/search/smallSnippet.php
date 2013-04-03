<style>
.ucynxqt385 td{padding-right:20px;vertical-align:middle;}
.ucynxqt385-title{width:300px;}
</style>
<?

//if($img = $p1->reflex_renderImg()) {
    echo "<table class='ucynxqt385' ><tr>";
    $preview = file::get($img)->preview(32,32);
    echo "<td><img src='$preview' /></td>";
    echo "<td><div class='hqxzkbcdpr-title' >{$p1->title()}</div></td>";
    echo "</tr></table>";
//} else {
    //echo $p1->title();
//}