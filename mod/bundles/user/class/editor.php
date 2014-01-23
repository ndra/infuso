<?

class user_editor extends reflex_editor {

    public function defaultBehaviours() {
        $ret = parent::defaultBehaviours();
        $ret[] = "user_editorBehaviour";
        return $ret;
    }
    
    public function beforeEdit() {
        return user::active()->checkAccess("user:editorStore");
    }
    
    
    public function beforeView() {
        return user::active()->checkAccess("user:editorView");    
    }
    
    public function beforeCollectionView() {
        return user::active()->checkAccess("user:editorCollectionView");    
    }
    
    public function renderListData() {
    
        $ret = $this->item()->title();
        if(!$this->item()->verified())
            $ret = "<span style='color:red;' >".$ret." - почта не подтверждена</span>";
           
        $roles = array();
        foreach($this->item()->roles() as $role) {
            if($role->code()!="guest") {
                $roles[] = "<i>{$role->title()}</i> ";
            }
        }
        if(sizeof($roles))
            $ret.= "<div style='color:gray;' >".implode(", ",$roles)."</div>";
            
        return $ret;
    }
    
    public function root() {

        $ret = array();

        if(user::active()->checkAccess("user:showInCatalogMenu")) {
            $ret[] = user::all()->title("Пользователи")->param("tab","user");
        }

        return $ret;
    }
    
}

class user_editorBehaviour extends mod_behaviour {

    public function inxExtraTabs() {
        return array(
            array (
                "type" => "inx.mod.user.editor.tools",
                "email" => $this->item()->data("email"),
                "userID" => $this->item()->id(),
                "lazy" => true,
                "title" => "Управление",
            )
        );
    }

    public function filters() {
        return array(
            user::all()->title("Все"),
            user::all()->neq("roles","")->neq("roles","guest")->title("С ролями"),
            user::all()->eq("roles",array("","guest"))->title("Гости"),
        );
    }
    
    public function inxBeforeForm() {
        return array(
            "type" => "inx.mod.user.editor.roles",
            "title" => "<div style='font-size:16px;font-weight:bold;' >Роли</div>",
            "userID" => $this->item()->id(),
        );
    }

    public function icon() {
        return "user";
    }

    public function quickSearch() {
        return "email";
    }

    public function actions() {
        return array(
            array("text"=>"Подтвердить почту",action=>"verify"),
            array("text"=>"Снять подтверждение почты",action=>"unverify"),
            array("text"=>"Войти как пользователь",action=>"login"),
        );
    }

    /**
     * Экшн подтверждения почты
     **/
    public function action_verify() {
        if(!user::active()->checkAccess("user:verifyEmail",array(
            "user" => $this->item(),
        ))) {
            mod::msg("Вы не можете подтвердить почту у этого пользователя.",1);
            return;
        }
        $this->item()->setVerification();
    }

    /**
     * Экшн снятия подтверждения почты
     **/
    public function action_unverify() {
        if(!user::active()->checkAccess("user:removeEmailVerification",array(
            "user" => $this->item(),
        ))) {
            mod::msg("Вы не можете снять подтверждение почты для этого пользователя.",1);
            return;
        }
        $this->item()->removeVerification();
    }

    /**
     * Экшн "Войти как пользователь"
     **/
    public function action_login() {
        if(!user::active()->checkAccess("user:loginAs",array(
            "user" => $this->item(),
        ))) {
            mod::msg("У вас нет прав для управления пользователями.",1);
            return;
        }
        $this->item()->activate();
        mod::msg("Вы авторизовались как {$this->item()->title()}");
    }

}
