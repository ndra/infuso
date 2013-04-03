<? 

echo "<select class='kb1t252a' >";
foreach($p1 as $mode) {
    $inject = $mode->active() ? "selected" : "";
    echo "<option value='{$mode->url()}' $inject >";
    echo $mode->title();
    echo "</option>";
}
echo "</select>";