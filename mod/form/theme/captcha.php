<? 

echo "<img src='/form_kcaptcha/index/name/{$field->ename()}' style='width:206px;height:80px;'><br/>";
$width = $field->width();
$inject = "style='width:{$width}px;'";

echo "<div class='njliyjoc0c-note' >";
echo "Введите число, изображенное на картинке";
echo "</div>";

echo "<input class='njliyjoc0c-textfield' $inject type='textfield' name='{$field->ename()}' value='{$field->evalue()}' />";