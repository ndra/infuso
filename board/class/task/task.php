<?

class board_task extends reflex {

    public function reflex_table() {

        return array(
            'name' => 'board_task',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ),array (
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
                    'class' => "user",
                ),array (
                    'name' => 'changed',
                    'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
                ),array (
                    'name' => 'projectID',
                    'type' => 'pg03-cv07-y16t-kli7-fe6x',
                    'class' => 'board_project',
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
                    'label' => 'Ответственный пользователь',
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
                    'type' => 'pg03-cv07-y16t-kli7-fe6x',
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
                    'name' => 'pauseTime',
                    'type' => 'yvbj-cgin-m90o-cez7-mv2j',
                    'label' => 'Суммарное время паузы',
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
        return reflex::get(get_class())
            ->asc("priority");
    }

    /**
     * Возвращает список видимых задач для активного пользователя
     **/
    public static function visible() {
        $list = self::all();

        if(user::active()->checkAccess("board/viewAllTasks")) {
            return $list;
        }

        $projects = board_project::visible()->limit(0)->idList();
        $list->eq("projectID",$projects);
        return $list;
    }

    /**
     * Возвращает задлачу по id
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
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
        return util::str($this->data("text"))->ellipsis(50)."";
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

    public function reflex_beforeStore() {

        // Устанавливаем новую дату изменения только если задача активна
        // Иначе мы можем влезть в статистику по прошлому периоду
        if($this->field("status")->changed()) {
            if($this->status()->active()) {
                $this->data("changed",util::now());
            }
            $this->data("paused",false);
            $this->data("pauseTime",0);

            $this->defer("handleStatusChanged");

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
                ->eq("status",board_task_status::STATUS_IN_PROGRESS)
                ->neq("id",$this->id());
            foreach($xtasks as $xtask) {
                $xtask->pause();
            }

        }

        mod::fire("board/taskChanged",array(
            "deliverToClient" => true,
            "taskID" => $this->id(),
            "sticker" => $this->stickerData(),
            "changed" => $changed,
        ));

    }

    /**
     * Делает рассылку на почту при изменении статуса
     **/
    public function handleStatusChanged() {

        $taskTextShort = util::str($this->data("text"))->ellipsis(100);
        $taskTextLong = util::str($this->data("text"))->ellipsis(1000);
        $params = array(
            "subject" => "{$this->responsibleUser()->title()} / {$this->project()->title()} / {$this->status()->title()} / $taskTextShort",
            "type" => "text/html",
        );

        $message = "";

		$user = user::active();
        $userpick = mod_url::current()->scheme()."://".mod_url::current()->domain().$user->userpick()->preview(50,50)->crop();
        $message.= "<table><tr>";
        $message.= "<td><img src='{$userpick}' ></td>";
        $message.= "<td>";
        $message.= "Проект: <b>".$this->project()->title()."</b><br/>";
        $message.= $this->status()->title()."<br/>";
        $logItem = $this->getLogCustom()->geq("created",util::now()->shift(-3))->one();
        $message.= $logItem->data("text");
        $message.= "</td>";
        $message.= "</tr></table>";

        $message.= "<div style='padding:10px;border:1px solid #ccc;background:#ededed;margin-top:10px;' >";
        $message.= $taskTextLong;
        $message.= "</div>";
        $params["message"] = $message;

        // Рассылка по всем
        user_subscription::mailByKey("board/statusChange",$params);
        // Рассылка подписанным на конкретный проект
        user_subscription::mailByKey("board/project-{$this->project()->id()}/statusChange",$params);

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

    /**
     * Временный метод для исправления структуры
     **/
    public function reindex() {
        // Если это подзадача, ставим проект как у эпика
        if($this->data("epicParentTask")) {
            $this->data("projectID",$this->pdata("epicParentTask")->data("projectID"));
        }
    }

    public function updateTimeSpent() {
        $this->data("timeSpent",$this->getLogCustom()->sum("timeSpent"));
    }

    /**
     * Возвращает потраченное но еще неучтенное времия
     * Если вы делавете задачу два часа, но еще не сделалт, timeSpent() вернет 0
     *
     **/
    public function timeSpentProgress() {

        $date = $this->pdata("changed");
        $d = util::now()->stamp() - $date->stamp();
        $d -= $this->data("pauseTime");

        if($this->data("paused")) {
            $d-= util::now()->stamp() - $this->pdata("paused")->stamp();
        }

        return $d;

    }

    public function getLogCustom() {
        return board_task_log::all()->eq("taskID",$this->id());
    }

    public function logCustom($text,$time=0,$type) {
        $this->getLogCustom()->create(array(
            "taskID" => $this->id(),
            "type" => $type,
            "text" => $text,
            "timeSpent" => $time,
        ));
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
        return board_task_status::get($this->data("status"));
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
            ->eq("status",board_task_status::STATUS_IN_PROGRESS)
            ->neq("id",$this->id());
        foreach($xtasks as $xtask) {
            $xtask->pause();
        }

        $time = util::now()->stamp() - $this->pdata("paused")->stamp();
        $time+= $this->data("pauseTime");
        $this->data("pauseTime",$time);
        $this->data("paused",null);
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
        return (bool)$this->data("paused");
    }

    /**
     * Возвращает голоса за задачу
     **/
    public function votes() {
        return board_task_vote::all()->eq("taskID",$this->id());
    }

    /**
     * Возвращает данные для стикера
     **/
    public function stickerData() {

        if($this->data("type")==1) {
            return array(
                "folder" => true,
            );
        }

        $ret = array(
            "backgroundImage" => null,
        );

        // Текст стикера
        $ret["text"] = "<b>{$this->id()}.</b> ";

        // Сколько задача висит в этом статусе
        if($this->status()->active()) {
            $d = (util::now()->stamp() - $this->pdata("changed")->stamp())/60/60/24;
            $d = round($d);
            if($d>=3) {
                $data["text"].= "<span style='background:red;color:white;display:inline-block;padding:0px 4px;' >$d</span> ";
            }
        }

        // Бонусные задачи
        if($this->data("bonus")) {
            $ret["text"].= "<span style='color:white;background:green;font-style:italic;padding:0px 4px;'>б</span> ";
        }

        // Просрочка
        $h = $this->hangDays();
        if($h>3) {
            $ret["text"].= "<span style='color:white;background:red;padding:0px 4px;'>$h</span> ";
        }

        $score = $this->votes()->avg("score");
        if($score) {

            $smiles = array(":(:(",":(",":|",":)",":):)");
            $html = $smiles[round($score)-1]." ";

            if(!$this->votes()->eq("ownerID",user::active()->id())->void()) {
               $html = "<span style='background:rgba(255,255,0,.5);padding:2px;' >{$html}</span>";
            }

            $ret["text"].= $html;

        }

        $ret["text"].= "<b>".$this->project()->title().".</b> ";
        $ret["text"].= util::str($this->data("text"))->ellipsis(200)->secure()."";

        // Статусная часть стикера
        $ret["info"] = "";
        $ret["info"].= round($this->timeSpent(),1);
        if($this->data("status")==board_task_status::STATUS_IN_PROGRESS) {
            $ret["info"].= "+".round($this->timeSpentProgress()/3600,1);
        }
        $ret["info"].= "/".round($this->timeScheduled(),1)."ч. ";

        // Цвет стикера
        $ret["color"] = $this->data("color");

        // Нижня подпись
        if($this->responsibleUser()->exists()) {
            $ret["bottom"] = "<nobr>".$this->responsibleUser()->title()."</nobr> ";
        }

        if($this->data("deadline")) {
            $ret["bottom"].= $this->pdata("deadlineDate")->left();
        }

        $ret["my"] = $this->responsibleUser()->id() == user::active()->id();

        $ret["id"] = $this->id();

        // Установленный дэдлайн
        if($this->data("deadline")) {
            $ret["backgroundImage"] = "/board/res/task-time.png";
        }
        $ret["deadlineDate"] = $this->data("deadlineDate");

        // Пропущенный дэдлайн
        $d = util::now()->stamp() - $this->pdata("deadlineDate")->stamp();
        if($this->data("deadline") && $d>0) {
            $ret["backgroundImage"] = "/board/res/task-time-fuckup.png";
        }

        if($this->data("hindrance")) {
            $ret["hindrance"] = true;
        }

        $ret["projectID"] = $this->project()->id();

        $ret["epic"] = $this->isEpic();

        // Наличие прикрепленных файлов
        if($this->data("files")) {
            $ret["attachment"] = true;
        }

        $ret["percentCompleted"] = $this->percentCompleted();

        if($this->data("paused")) {
            $ret["backgroundImage"] = "/board/res/img/icons64/pause.png";
        }

        // Кнопки задачи
        $ret["tools"] = array();
        switch($this->status()->id()) {

            case board_task_status::STATUS_IN_PROGRESS:
                if(!$this->paused()) {
                    $ret["tools"][] = "pause";
                } else {
                    $ret["tools"][] = "resume";
                }
                $ret["tools"][] = "done";
                break;

            case board_task_status::STATUS_NEW:
                $ret["tools"][] = "take";
                break;

            case board_task_status::STATUS_CHECKOUT:
                $ret["tools"][] = "complete";
                $ret["tools"][] = "revision";
                break;
        }
        $ret["tools"][] = "vote";

        return $ret;
    }

}
