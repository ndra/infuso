<?

$id = util::id();
$inject = $field->value() ? "checked='true'" : "";

<input class='x96964wb4g' name='{$field->ename()}' value='1' $inject type='checkbox' id='$id' />
<label for='$id' >{$field->label()}</label>