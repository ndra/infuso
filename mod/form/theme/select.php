<? 

<select class='njliyjoc0c-select' name='{$field->ename()}'>

    foreach($field->options() as $key=>$val) {
        $inject = $key==$field->value() ? "selected='true'" : "";
        <option $inject value='$key'>$val</option>
    }
    
</select>