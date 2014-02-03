<?

namespace Infuso\Board;

use \User;
use \mod, \Util;

class Task extends \Infuso\ActiveRecord\Record {

    public function reflex_table() {
    
        return array(
            'name' => 'board_task',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ),array (
                    'name' => 'dataHash',
                    'type' => 'string',
                ), array (
                    'name' => 'text',
                    'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
                    'editable' => '1',
                ),array (
                    'name' => 'color',
                    'type' => 'v324-89xr-24nk-0z30-r243',
                    'editable' => '1',
                    'label' => 'Цвет',
                ),array (
                    'name' => 'status',
                    'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
                    'editable' => '1',
                ),array (
                    'name' => 'priority',
                    'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
                    'label' => 'Приоритет',
                ),array (
                    'name' => 'created',
                    'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
                ),array (
                    'name' => 'creator',
                    'type' => 'link',
                    'class' => User::inspector()->className(),
                ),array (
                    'name' => 'changed',
                    'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
                ),array (
                    'name' => 'projectID',
                    'type' => 'pg03-cv07-y16t-kli7-fe6x',
                    'class' => Project::inspector()->className(),
                ),array (
                    'name' => 'bonus',
                    'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
                    'label' => 'Бонус',
                ),array (
                    'name' => 'timeScheduled',
                    'type' => 'yvbj-cgin-m90o-cez7-mv2j',
                    'label' => 'Планируемое время',
                ),array (
                    'name' => 'timeSpent',
                    'type' => 'yvbj-cgin-m90o-cez7-mv2j',
                    'label' => 'Потрачено времени',
                ),array (
                    'name' => 'responsibleUser',
                    'type' => 'pg03-cv07-y16t-kli7-fe6x',
                    'label' => User::inspector()->className(),
                    "class" => "user",
                ),array (
                    'name' => 'deadline',
                    'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
                ),array (
                    'name' => 'deadlineDate',
                    'type' => 'ler9-032r-c4t8-9739-e203',
                ),array (
                    'name' => 'epic',
                    'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
                    'label' => 'Эпик',
                ),array (
                    'name' => 'epicParentTask',
                    'type' => Task::inspector()->className(),
                    'label' => 'reflex_task',
                    'class' => 'board_task',
                ),array (
                    'name' => 'hindrance',
                    'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
                    'label' => 'Помеха',
                ),array (
                    'name' => 'paused',
                    'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
                    'label' => 'Пауза',
                ),array (
                    'name' => 'files',
                    'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
                    'label' => 'Количество файлов',
                ),array (
                    'name' => 'notice',
                    'type' => 'v324-89xr-24nk-0z30-r243',
                    'label' => 'Заметка',
                ), array(
                    "name" => "type",
                    "type" => "select",
                    "options" => self::enumTypes(),
                )
            ),
        );
    }

    public function enumTypes() {
        return array(
            0 => "Задача",
            1 => "Группа",
            2 => "Todo"
        );
    }

    /**
     * Возвращает список всех задач
     **/
    public static function all() {
        return \Infuso\ActiveRecord\Record::get(get_class())
            ->addBehaviour("Infuso\\Board\\CollectionBehaviour")
            ->asc("priority");
    }

    /**
     * Возвращает список видимых задач для активного пользователя
     **/
    public static function visible() {
        $projects = Project::visible();
        return self::all()->joinByField("projectID",$projects);
    }

    /**
     * Возвращает задлачу по id
     **/
    public static function get($id) {
        return \Infuso\ActiveRecord\Record::get(get_class(),$id);
    }

    public function reflex_url() {
        return "/board/#task/id/".$this->id();
    }

    public function project() {
        return $this->pdata("projectID");
    }

    public function reflex_parent() {
        return $this->project();
    }

    public static function reflex_root() {
        return self::all()->title("Все задачи")->param("tab","system");
    }

    public function reflex_children() {
        return array(
            $this->getLogCustom()->title("Затраченное время"),
            $this->subtasks()->title("Подзадачи"),
        );
    }

    public function reflex_title() {
        return \util::str($this->data("text"))->ellipsis(50)."";
    }

    public function text() {
        return $this->data("text");
    }

    public function responsibleUser() {
        return $this->pdata("responsibleUser");
    }

    public function reflex_beforeCreate() {
        $this->data("changed",util::now());
        $this->data("created",util::now());
        $this->data("creator",user::active()->id());
    }

    public function reflex_afterCreate() {
        $this->log("Создано");
    }

    public $taskEventsSuspended = false;

    public function suspendTaskEvents() {
        $this->taskEventsSuspended = true;
    }

    public function unsuspendTaskEvents() {
        $this->taskEventsSuspended = false;
    }

    public function isTaskEventsSuspended() {
        return $this->taskEventsSuspended;
    }

    public function reflex_beforeStore() {

        $this->data("dataHash",\util::id());

        // Устанавливаем новую дату изменения только если задача активна
        // Иначе мы можем влезть в статистику по прошлому периоду
        if($this->field("status")->changed()) {
        
            if($this->status()->active()) {
                $this->data("changed",util::now());
            }
            
            $this->data("paused",false);

			// Отправляем рассылку про выполненные сообщения
			if($this->field("status")->initialValue() == TaskStatus::STATUS_IN_PROGRESS
				&& in_array($this->data("status"),array(TaskStatus::STATUS_COMPLETED, TaskStatus::STATUS_CHECKOUT))) {
            	$this->defer("handleCompleted");
			}
			
			// Отправляем рассылку про возвращанные на доработку сообщения
			if($this->field("status")->initialValue() == TaskStatus::STATUS_CHECKOUT
				&& in_array($this->data("status"),array(TaskStatus::STATUS_NEW))) {
            	$this->defer("handleRevision");
			}

			// При переходи задачи в статус к исполнению она ставится на первое место
			if($this->data("status") == TaskStatus::STATUS_NEW) {
			    $min = board_task::all()->eq("status",TaskStatus::STATUS_IN_PROGRESS)->min("priority");
			    $this->data("priority",$min - 1);
			}

			// Если взяли задачу - запускаем таймер
			if($this->data("status") == TaskStatus::STATUS_IN_PROGRESS) {
                $this->startTimer();
			}

			// Если задача перестала выполняться - останавливаем таймер
			if($this->field("status")->initialValue() == TaskStatus::STATUS_IN_PROGRESS) {
                $this->stopTimer();
                $this->timeLog()->data("charged",1);
			}

        }

        // Если это подзадача, ставим проект как у эпика
        if($this->data("epicParentTask")) {
            $this->data("projectID",$this->pdata("epicParentTask")->data("projectID"));
        }

        // Собираем список измененных полей
        $changed = array();
        foreach($this->fields() as $field) {
            if($field->changed()) {
                $changed[] = $field->name();
            }
        }

        // Если статус задачи "к исполнению", ответственным лицом становится текущий пользователь.
        if($this->field("status")->changed() && $this->status()->id()==1) {
            $this->data("responsibleUser",user::active()->id());

            $xtasks = board_task::all()
                ->eq("responsibleUser",user::active()->id())
                ->eq("status",TaskStatus::STATUS_IN_PROGRESS)
                ->neq("id",$this->id());
            foreach($xtasks as $xtask) {
                $xtask->pause();
            }
        }

        if(!$this->isTaskEventsSuspended()) {

            mod::fire("board/taskChanged",array(
                "deliverToClient" => true,
                "taskID" => $this->id(),
                "sticker" => $this->stickerData(),
                "statusText" => $this->statusText(),
                "changed" => $changed,
            ));
        }

    }
    
    public function statusText() {
    
        if($this->paused()) {
            return "На паузе";
        }
    
        return $this->status()->title();
    }

    /**
     * Делает рассылку на почту при изменении статуса
     **/
    public function handleCompleted() {

        $taskTextShort = util::str($this->data("text"))->ellipsis(100);
        $taskTextLong = util::str($this->data("text"))->ellipsis(1000);
        $params = array(
            "subject" => "{$this->responsibleUser()->title()} / {$this->project()->title()} / {$this->status()->action()} / $taskTextShort",
            "type" => "text/html",
            "completedBy" => user::active()->id(),
        );

        $message = "";
        
        $host = mod_url::current()->scheme()."://".mod_url::current()->domain();

		$user = user::active();
        $userpick = $host.$user->userpick()->preview(50,50)->crop();
        $message.= "<table><tr>";
        $message.= "<td><img src='{$userpick}' ></td>";
        $message.= "<td>";
        $message.= "Проект: <b>".$this->project()->title()."</b><br/>";
        $logItem = $this->getLogCustom()->geq("created",util::now()->shift(-3))->one();
        $message.= $logItem->data("text");
        $message.= "</td>";
        $message.= "</tr></table>";

        foreach($logItem->files() as $file) {
            $message.= "<a href='{$host}{$file}' style='margin:0 10px 10px 0;' >";
            $message.= "<img src='{$host}{$file->preview(128,128)->crop()}' />";
            $message.= "</a>";
        }

        $message.= "<div style='padding:10px;border:1px solid #ccc;background:#ededed;margin-top:10px;' >";
        $message.= $taskTextLong;
        $message.= "</div>";
        $params["message"] = $message;

        // Рассылка подписанным на конкретный проект
        user_subscription::mailByKey("board/project-{$this->project()->id()}/taskCompleted",$params);

    }
    
    public function handleRevision() {
        
        $user = $this->responsibleUser();
        $taskText = util::str($this->text())->ellipsis(100);
        $reason = $this->getLogCustom()->one()->text();
        $url = $this->url();
        $user->mailer()
            ->subject("Задача {$this->id()} <a href='{$url}' >«{$taskText}»</a> отправлена да доработку. Причина: {$reason}")
			->send();
        
    }

    public function fireChangedEvent() {
        mod::fire("board/taskChanged",array(
            "deliverToClient" => true,
            "taskID" => $this->id(),
            "sticker" => $this->stickerData(),
            "changed" => array(),
        ));
    }

    public function reflex_afterStore() {
        if($this->data("epicParentTask")) {

            $task = $this->pdata("epicParentTask");
            $task->fireChangedEvent();
            $task->data("responsibleUser",0);

        }
    }

    public function updateTimeSpent() {
        $this->data("timeSpent",$this->getLogCustom()->sum("timeSpent"));
    }

    /**
     * Возвращает потраченное но еще неучтенное времия
     * Если вы делавете задачу уже два часа, но еще не сделали, timeSpent() вернет 2 * 3600
     * Время возвращается в секундах
     **/
    public function timeSpentProgress() {
    
        if($this->status()->id() != TaskStatus::STATUS_IN_PROGRESS) {
            return 0;
        }

        // Предыдущие интервалы
        $a = $this->timeLog()->eq("charged",0)->notnull("end")->select("SUM(TIMESTAMPDIFF(SECOND,`begin`,`end`))");
        $a = end(end($a))*1;

        // Текущий интервал
        $b = $this->timeLog()->eq("charged",0)->isnull("end")->select("SUM(TIMESTAMPDIFF(SECOND,`begin`,now()))");
        $b = end(end($b))*1;

        return $a + $b;

    }

    public function getLogCustom() {
        return TaskLog::all()->eq("taskID",$this->id());
    }

    public function logCustom($params) {
        $this->getLogCustom()->create(array(
            "taskID" => $this->id(),
            "type" => $params["type"],
            "text" => $params["text"],
            "timeSpent" => $params["time"],
            "files" => $params["files"],
        ));
    }

    public function timeLog() {
        return board_task_time::all()->eq("taskID",$this->id());
    }

    public function startTimer() {
        $this->timeLog()->create(array());
    }

    public function stopTimer() {
        $this->timeLog()->one()->data("end",util::now());
    }

    /**
     * Возвращает время, потраченное на задачу
     * Суммируются время, потраченное на задачу и на субзадачи
     **/
    public function timeSpent() {
        return $this->data("timeSpent") + $this->subtasks()->sum("timeSpent");
    }

    /**
     * Возвращает планируемое время
     **/
    public function timeScheduled() {
        return $this->data("timeScheduled");
    }

    /**
     * Возвращает статус задача (объект)
     **/
    public function status() {
        return TaskStatus::get($this->data("status"));
    }

    /**
     * Возвращает коллекцию подзадач
     **/
    public function subtasks() {
        return self::all()->eq("epicParentTask",$this->id());
    }

    /**
     * Возвращает число, показывающее сколько дней задача не меняла статус
     **/
    public function hangDays() {
        return round((util::now()->stamp() - $this->pdata("changed")->stamp())/60/60/24);
    }

    public function isEpic() {
        return !$this->subtasks()->void();
    }

    /**
     * Возвращает процент выполненния задачи
     **/
    public function percentCompleted() {

        $a = $this->timeSpent();
        $b = $this->timeScheduled();

        if(!$b) {
            return 0;
        }

        $ret = $a / $b * 100;

        if($ret > 100) {
            $ret = 100;
        }

        return $ret;
    }

    /**
     * Ставит задачу на паузу
     **/
    public function pause() {

        if($this->data("paused")) {
            return;
        }

        $this->data("paused",util::now());
        $this->stopTimer();

    }

    /**
     * снимает задачу с паузы
     **/
    public function resume() {

        if(!$this->data("paused")) {
            return;
        }

        // Ставим остальные задачи на паузу
        $xtasks = board_task::all()
            ->eq("responsibleUser",user::active()->id())
            ->eq("status",TaskStatus::STATUS_IN_PROGRESS)
            ->neq("id",$this->id());
        foreach($xtasks as $xtask) {
            $xtask->pause();
        }

        $this->data("paused",null);
        $this->startTimer();
    }

    /**
     * Ставит задачу на паузу / снимает с паузы
     **/
    public function pauseToggle() {
        if($this->data("paused")) {
            $this->resume();
        } else {
            $this->pause();
        }
    }

    public function uploadFilesCount() {
        $n = $this->storage()->files()->count();
        $this->data("files",$n);
    }

    /**
     * Фозвращает флаг того что задача стоит на паузе
     **/
    public function paused() {

        if($this->status()->id() != TaskStatus::STATUS_IN_PROGRESS) {
            return false;
        }

        return (bool)$this->data("paused");
    }

    /**
     * Возвращает голоса за задачу
     **/
    public function votes() {
        return board_task_vote::all()->eq("taskID",$this->id());
    }

    /**
     * Возвращает джанные стикера задачи
     * Кэширует результат
     **/
    public function stickerData() {

        $key = "board/stickerData/".$this->id()."/".$this->data("dataHash")."/".user::active()->id();
        $cacheService = mod::service("cache");
        $cached = $cacheService->get($key);
        if($cached) {
            $cached = json_decode($cached,1);
            return $cached;
        }

        $data = $this->stickerDataNoCache();
        $loader = new \mod_confLoader_json();
        $cached = $loader->write($data);

        // Кэшируем данные задачи на 10 минут
        $cacheService->set($key,$cached,600);

        return $data;

    }

    /**
     * Возвращает данные для стикера
     **/
    public function stickerDataNoCache() {

        if($this->data("type")==1) {
            return array(
                "folder" => true,
            );
        }

        $ret = array();

		$ret["id"] = $this->id();

        // Текст стикера
        $ret["text"] = \util::str($this->data("text"))->ellipsis(200)->secure()."";
        
        // Проект
        $ret["project"] = array(
            "id" => $this->project()->id(),
			"title" => $this->project()->title(),
            "icon" => $this->project()->icon()->preview(16,16),
		);
		
		// Ответственный пользователь
		$ret["responsibleUser"] = array(
		    "nick" => $this->responsibleUser()->title(),
		    "userpic" => (string)$this->responsibleUser()->userpick()->preview(16,16)->crop(),
		);

		// Своя задача
        $ret["my"] = $this->responsibleUser()->id() == user::active()->id();
        
        // Статус
        $ret["status"] = array(
            "id" => $this->status()->id(),
            "title" => $this->statusText(),
		);
		
        // Цвет стикера
        $ret["color"] = $this->data("color");

        // Хапланированное и потраченное время
        $ret["timeSpent"] = round($this->timeSpent(),2);
        $ret["timeSpentProgress"] = round($this->timeSpentProgress()/3600,2);
        $ret["timeScheduled"] = round($this->timeScheduled(),2);

        // Установленный дэдлайн
        $ret["deadlineDate"] = $this->data("deadlineDate");
        $ret["deadline"] = $this->data("deadline");
        
        if($this->data("deadline")) {
        	$ret["deadlineMissed"] = util::now()->stamp() > $this->pdata("deadlineDate")->stamp();
        }

        // Пропущенный дэдлайн
        $d = util::now()->stamp() - $this->pdata("deadlineDate")->stamp();
        // @todo сделать

        // Эпик (задача с подзадачами)
        $ret["epic"] = $this->isEpic();

        // Наличие прикрепленных файлов
        if($this->data("files")) {
            $ret["attachment"] = true;
        }
        
		// Стоит ли задача на паузе
        $ret["paused"] = $this->paused();

        $ret["percentCompleted"] = $this->percentCompleted();
        
        $ret["images"] = array();
		foreach($this->storage()->files() as $file) {
            $ret["images"][] = array(
                "x30" => $file->preview(30,30),
                "original" => $file,
			);
        }

        // Кнопки задачи (видны только если можно изменять задачу)

        $ret["tools"] = $this->tools();
        
        return $ret;
    }

    /**
     * Возвращает массив допустимых операций с задачей
     **/
    public function tools() {

        if(!user::active()->checkAccess("board/updateTaskParams",array(
            "task" => $this,
        ))) {
            return array();
        }

        $tools = array(
            "main" => array(),
            "additional" => array(),
        );

        switch($this->status()->id()) {

            case TaskStatus::STATUS_IN_PROGRESS:

                if(!$this->paused()) {
                    $tools["main"][] = "pause";
                } else {
                    $tools["main"][] = "resume";
                }
                $tools["main"][] = "done";

                $tools["additional"][] = "stop";
                $tools["additional"][] = "problems";
                $tools["additional"][] = "cancel";

                break;

            case TaskStatus::STATUS_BACKLOG:

                if($this->isEpic()) {
                    $tools["main"][] = "subtask";
                    $tools["additional"][] = "done";
                } else {
                    $tools["main"][] = "take";
                }

                $tools["additional"][] = "problems";
                $tools["additional"][] = "cancel";

                break;

            case TaskStatus::STATUS_DEMAND:

                $tools["main"][] = "add";
                $tools["main"][] = "take";

                $tools["additional"][] = "problems";
                $tools["additional"][] = "cancel";

                break;

            case TaskStatus::STATUS_CHECKOUT:

                $tools["main"][] = "complete";
                $tools["main"][] = "revision";
                $tools["additional"][] = "problems";
                $tools["additional"][] = "cancel";
                break;

            case TaskStatus::STATUS_COMPLETED:

                $tools["additional"][] = "revision";
                break;

        }

        return $tools;
    }
    
    /**
     * Возвращает список тэгов задачи
     **/
    public function tags() {
        return board_task_tag::all()->eq("taskID",$this->id());
    }
    
	/**
	 * Добавляет в задачу тэг
	 **/
    public function addTag($tagID) {
    
        if(!$this->exists()) {
            throw new Exception("board_task::addTag - Task not exists");
        }

        $tag = $this->tags()->eq("tagID",$tagID)->one();
        if(!$tag->exists()) {
            $tag = \Infuso\ActiveRecord\Record::create("board_task_tag",array(
                "taskID" => $this->id(),
                "tagID" => $tagID,
			));
        }
        
        mod::fire("board/tagsChanged",array(
            "taskID" => $this->id(),
            "deliverToClient" => true
		));
    
	}
	
	/**
	 * Убирает из задачи тэг
	 **/
    public function removeTag($tagID) {
    
        if(!$this->exists()) {
            throw new Exception("board_task::removeTag - Task not exists");
        }
    
        $tag = $this->tags()->eq("tagID",$tagID)->one();
        $tag->delete();
        
        mod::fire("board/tagsChanged",array(
            "taskID" => $this->id(),
            "deliverToClient" => true
		));
	}
	
	/**
	 * Отмечена ли эта задача тэгом
	 **/
	public function tagExists($tagID) {
	    return $this->tags()->eq("tagID",$tagID)->one()->exists();
	}
	
    /**
	 * Обновляет тэг (добавляет-удаляет тэг автоматически)
	 **/
	public function updateTag($tagID,$value) {
	    if($value) {
	        $this->addTag($tagID);
	    } else {
	        $this->removeTag($tagID);
	    }
	}

	public function tryAutocomplete() {

		// Если задача не на проверке - выходим
	    if($this->data("status") != TaskStatus::STATUS_CHECKOUT) {
	        return;
	    }
	
	    // Закрываем задачи у которых есть родители
	    if($this->data("epicParentTask")) {
	        $this->data("status",TaskStatus::STATUS_COMPLETED);
	        $this->logCustom("Закрыто автоматически");
	        return;
	    }
	    
	    $days = $this->project()->data("completeAfter");
	    if(!$days) {
	        return;
	    }
	    
	    $taskDays = (util::now()->date()->stamp() - $this->pdata("changed")->date()->stamp()) / 3600 / 24;
	    if($taskDays >= $days) {
	        $this->data("status",TaskStatus::STATUS_COMPLETED);
	        $this->logCustom("Закрыто автоматически");
	    }
	
	}

}
