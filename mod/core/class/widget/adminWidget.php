<? class mod_widget_adminWidget extends admin_widget {

public function exec() {

	if(mod_superadmin::check()) {
	    $url = mod_action::get("mod_console")->url();
		echo "<h2><a href='{$url}' >Консоль</a></h2>";
	}
	
	if(mod_superadmin::check()) {
		$url = mod_action::get("mod_log_browser")->url();
		echo "<a href='{$url}' >Лог</a> ";
	}
	
	if(mod_superadmin::check()) {
		$url = mod_action::get("mod_admin_behavioursBrowser")->url();
		echo "<a href='{$url}' >Поведения</a> ";
	}
	
	if(mod_superadmin::check()) {
		$url = mod_action::get("mod_admin_events")->url();
		echo "<a href='{$url}' >События</a> ";
	}
	
    if(mod_superadmin::check()) {
		$url = mod_action::get("mod_conf_controller")->url();
		echo "<a href='{$url}' >Конфигурация</a> ";
	}

    if(mod_superadmin::check()) {
		$url = mod_action::get("mod_conf_controller","componentsVisual")->url();
		echo "<a href='{$url}' ><nobr>Настройкка компонентов</nobr></a> ";
	}
	
	if(mod_superadmin::check()) {
		$url = mod_action::get("mod_cache_admin")->url();
		echo "<a href='{$url}' >Кэш</a> ";
	}
	
	if(mod_superadmin::check()) {
		$url = mod_action::get("mod_cron_log")->url();
		echo "<a href='{$url}' >Крон</a>";
	}

}

}
