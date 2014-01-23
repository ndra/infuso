<? 

<div class='suxdi6tkea' >
    
    foreach($item->table()->fieldGroups() as $group) {
    
        <div class='group' >
        
            <h2>{$group->title()}</h2>
        
            <table>
                foreach($group->fields() as $field) {
                    <tr>
                        <td>{$field->name()}</td>
                        <td>{$field->typeName()}</td>
                        <td>{$field->label()}</td>
                        <td>{$field->indexEnabled()}</td>
                        <td>{$field->visible()}</td>
                        <td>{$field->editable()}</td>
                    </tr>
                }
            </table>
        
        </div>
    
    }

</div>