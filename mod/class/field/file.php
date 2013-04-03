<?

class mod_field_file extends mod_field {

    public function typeID() {
        return "knh9-0kgy-csg9-1nv8-7go9";
    }

    public function typeName() {
        return "Файл";
    }

    public function mysqlType() {
        return "varchar(255)";
    }

    public function mysqlIndexType() {
        return "index";
    }

    public function editorInx() {
        return array(
            "type" => "inx.mod.file.field",
            "value" => $this->value(),
        );
    }

    public function tableCol() { return array(
        "type" => "image",
    ); }

    public function tableRender() {
        return $this->pvalue()->preview(16,16)->get();
    }

    public function pvalue() {
        return file::get($this->value());
    }
    
    public function editorInxDisabled() {
        $file = file::get($this->rvalue());
        $ext = strtolower($file->ext());
        if(in_array($ext,array("jpg","gif","png"))){
            $preview = $file->preview(100,100)->crop();
            return array(
                "type" => "inx.panel",
                "html" => "<img src='$preview'>", 
                "labelAlign" => "left",
                "style" => array("border" => 0 )   
            );
        }else{ 
            return array(
                "type" => "inx.mod.reflex.fields.readonly",
                "value" => $this->rvalue(),
            );
        }
    }

}
