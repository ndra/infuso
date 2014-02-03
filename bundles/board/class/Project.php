<?

namespace Infuso\Board;

class Project extends \Infuso\ActiveRecord\Record {

    public function reflex_table() {

        return array (
            'name' => "board_project",
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ),array (
                    'name' => 'title',
                    'type' => 'v324-89xr-24nk-0z30-r243',
                    'editable' => '1',
                    'label' => '',
                    'default' => '',
                ), array (
                    'name' => 'priority',
                    'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
                    'editable' => '1',
                    'label' => 'Приоритет',
                ), array (
                    'name' => 'icon',
                    'type' => 'file',
                    'editable' => '1',
                    'label' => 'Иконка',
                ), array (
                    'name' => 'url',
                    'type' => 'string',
                    'editable' => '1',
                    'label' => 'Адрес сайта',
                ), array (
                    'name' => 'completeAfter',
                    'type' => 'bigint',
                    'editable' => '1',
                    'label' => 'Закрывать задачи после (дней)',
                ),
            ),
        );

    }
    
    /**
     * Возвращает флаг наличия у активного пользователя подписки на этот проект
     **/
	public function isActiveUserHaveSubscription() {
	    $subscriptionKey = "board/project-{$this->id()}/taskCompleted";
	    return !user::active()->subscriptions()->eq("key",$subscriptionKey)->void();
	}

	/**
	 * Возвращает список всех проектов
	 **/
	public static function all() {
		return \Infuso\ActiveRecord\Record::get(get_class())->desc("priority");
	}

	/**
	 * Возвращает список проектов, видимых для активного пользователя
	 **/	 	
	public static function visible() {

		if(\user::active()->checkAccess("board:viewAllProjects")) {
			$projects = Project::all();
        } else {

            $access = board_access::all()
                ->eq("userID",user::active()->id())
                ->neq("userID",0);

			$projects = Project::all()->eq("id",$access->distinct("projectID"));
        }

		return $projects;
	}

    /**
     * Возвращает проект по id
     **/	     
	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public function tasks() {
		return board_task::all()->eq("projectID",$this->id());
	}

	public function customer() {
		return $this->pdata("customerUserID");
	}

	public static function reflex_root() {
	    return self::all()->title("Проекты")->param("tab","system");
	}

	public function reflex_children() {
	    return array(
	        $this->tasks()->title("Задачи"),
	    );
	}

    public function icon() {
        return $this->pdata("icon");
    }

    public function loadFavicon() {

        if(!$url = trim($this->data("url"))) {
            return;
        }

        $img = imagecreatefrompng("http://www.google.com/s2/favicons?domain={$url}");
        imagesavealpha($img,true);

        $tmp = file::tmp()."favicon.png";
        imagepng($img,file::get($tmp)->native());

        $icon = $this->storage()->add($tmp,"favicon.png");
        $this->data("icon",$icon);

    }
	
}
