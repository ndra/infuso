<? 

foreach ($field->options() as $key=>$item){
    $inject = $key==$field->value() ? "checked='true'" : "";
    $id = util::id();
    
    <div>
        <input name='{$field->name()}' $inject value='$key' id='$id' type='radio' />
        <label for='$id' >$item</label>
    </div>
}