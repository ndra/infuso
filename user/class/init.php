<?

/**
 * Класс-инициализатор
 **/
class user_init implements mod_handler {

    /**
     * Метод, в котором реализуется бизнес-логика инициализации
     **/
    public function on_mod_beforeInit() {
    
        // Очищаем список операций
        mod::msg("Removing roles and operations");
        user_operation::all()->delete();
        user_role::all()->delete();
        
        // Создаем роль по умолчанию
        $role = user_role::create("guest");
        $role->data("title","Гость");
        
        // Создаем роль по умолчанию
        $role = user_role::create("admin");
        $role->data("title","Администратор");
        
        // Операции в админке

        $o = user_operation::create("user:managerUsers","Управление пользователями");
        $o->appendTo("admin");

        $o = user_operation::create("user:showInCatalogMenu","Отбражение пользователей в меню каталога");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:viewRoles","Просмотр ролей");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:addRole","Добавление роли");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:deleteRole","Удаление роли");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:editorChangeEmail","Изменение почты через админку");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:editorChangePassword","Изменение пароля через админку");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:editorStore", "Сохранение пользователя через админку");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:editorView", "Просмотр пользователя через админку");
        $o->appendTo("user:managerUsers");
        
        $o = user_operation::create("user:editorCollectionView", "Просмотр списка пользователей через админку");
        $o->appendTo("user:managerUsers");

        $o = user_operation::create("user:verifyEmail","Подтверждение почты");
        $o->appendTo("user:managerUsers");

        $o = user_operation::create("user:removeEmailVerification","Снятие подтверждения почты");
        $o->appendTo("user:managerUsers");

        $o = user_operation::create("user:loginAs","Войти как пользователь");
        $o->appendTo("user:managerUsers");

        // Пользовательские операции
        
        $o = user_operation::create("user:unlinkSocial");
        $o->addBusinessRule('return $social->user()->id() == user::active()->id();');
        $o->appendTo("guest");
        
        $o = user_operation::create("user:unsubscribe");
        $o->addBusinessRule('return $subscribtion->user()->id() == user::active()->id();');
        $o->appendTo("guest");
        
        // Таб для каталога
        
        reflex_editor_rootTab::create(array(
            "title" => "Пользователи",
            "name" => "user",
            "icon" => "/user/res/icons/48/user.png",
		));

    }

    /**
     * Приоритет инициализации
     **/
    public function priority() {
        return 0;
    }

}
