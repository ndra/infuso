<?

class reflex_domain_editor extends reflex_editor {

    public function beforeEdit() {
        return user::active()->checkAccess("admin");
    }  
    
}
