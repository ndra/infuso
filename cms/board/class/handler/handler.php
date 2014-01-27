<?

class board_handler implements mod_handler {

    public function on_mod_init() {
        
        // Создаем роль
        
        $role = user_role::create("boardUser");
        $role->data("title","Пользователь доски");
        
        // Операции в доске
        
        $o = user_operation::create("board:viewAllProjects");
        $o->appendTo("boardUser");
        
        // Операции с проектами
        
        user_operation::create("board/createProject","Создание проекта в доске")
            ->appendTo("boardUser");

        user_operation::create("board/updateProject","Редактирование проекта в доске")
            ->appendTo("boardUser");

        user_operation::create("board/viewAllProjects","Создание проекта в доске")
            ->appendTo("boardUser");

        user_operation::create("board/viewGrantedProject","Просмотр проекта (когда предоставлен доступ к проекту) ")
            ->addBusinessRule('$projects = board_project::visible()->eq("id",$project->id()); return !$projects->void(); ')
			->appendTo('guest');

        user_operation::create("board/viewProject","Просмотр проекта")
            ->appendTo("board/viewAllProjects")
            ->appendTo("board/viewGrantedProject");
        
        // Операции с задачами

        user_operation::create("board/task/fullAccess","Редактирование задачи (когда предоставлен доступ к проекту)")
            ->addBusinessRule('return board_access::all()->eq("userID",$user->id())->eq("editTasks",1)->eq("projectID",$task->project()->id())->one()->exists();')
			->appendTo('guest');

        user_operation::create("board/task/limitedAccess","Редактирование задачи (ограниченый доступ к проекту)")
            ->addBusinessRule('return false;')
			->appendTo('guest');

        user_operation::create("board/editAnyTask","Редактирование любой задачи")
			->appendTo('boardUser');

        user_operation::create("board/editTask","Редактирование задачи")
			->appendTo('board/editAnyTask')
            ->appendTo('board/task/fullAccess');

        user_operation::create("board/viewAllTasks","Просмотр задач без ограничений")
			->appendTo('boardUser');

        user_operation::create("board/viewGrantedTask","Просмотр задачи (когда предоставлен доступ к проекту) ")
            ->addBusinessRule('$tasks = board_task::visible()->eq("id",$task->id()); return !$tasks->void(); ')
			->appendTo('guest');

        user_operation::create("board/viewTask","Просмотр задачи")
            ->appendTo("board/viewAllTasks")
			->appendTo('board/viewGrantedTask');
        
        user_operation::create("board/updateTaskParams","Обновление полей задачи")
            ->appendTo("board/editTask");

        user_operation::create("board/changeTaskProject","Изменение проекта задачи")
            ->appendTo("boardUser");
        
        user_operation::create("board/getTaskParams","Получение полей задачи")
            ->appendTo("board/viewTask");

        user_operation::create("board/getTaskTime","Получение времени, потраченного на задачу")
            ->appendTo("board/editTask");

        user_operation::create("board/getEpicSubtasks","Получение списка подзадач эпика")
            ->appendTo("board/viewTask");

        user_operation::create("board/addEpicSubtask","Добавление подзадачи эпика")
            ->appendTo("board/editTask");

        /*user_operation::create("board/changeTaskStatus","Изменение статуса задачи")
            ->addBusinessRule("if(\$status == board_task_status::STATUS_IN_PROGRESS && !\$task->subtasks()->void()) \$this->error('Нельзя взять эпик. Возьмите подзадачу.'); ")
            ->addBusinessRule("return true;")
            ->appendTo("board/editTask"); */

        user_operation::create("board/takeTask", "Взять задачу")
            ->appendTo("boardUser");

        user_operation::create("board/stopTask", "Приостановить задачу")
            ->appendTo("boardUser");

        user_operation::create("board/doneTask", "Выполнить задачу")
            ->appendTo("boardUser");

        user_operation::create("board/completeTask", "Проверить задачу")
            ->appendTo("boardUser")
            ->appendTo("board/task/fullAccess");

        user_operation::create("board/revisionTaskToBacklog", "Вернуть задачу в бэклог")
            ->appendTo("boardUser");

        user_operation::create("board/revisionTaskToDemand", "Вернуть задачу в заявки")
            ->appendTo("board/task/fullAccess");

        user_operation::create("board/cancelTask", "Отменить задачу")
            ->appendTo("boardUser")
            ->appendTo("board/task/fullAccess");

        user_operation::create("board/task/moveToBacklog", "Переместить задачу в бэклог")
            ->appendTo("boardUser");

       user_operation::create("board/newTaskInAnyProject","Создание задачи в любом проекте")
            ->appendTo("boardUser");

       user_operation::create("board/newTaskInGrantedProject","Создание задачи в проекте, к которому предоставлен доступ")
            ->addBusinessRule('return board_access::all()->eq("userID",$user->id())->eq("editTasks",1)->eq("projectID",$project->id())->one()->exists();')
            ->appendTo("guest");

        user_operation::create("board/newTask","Создание задачи")
            ->appendTo("board/newTaskInAnyProject")
            ->appendTo("board/newTaskInGrantedProject");
            
        user_operation::create("board/newHindrance","Создание помехи")
            ->appendTo("boardUser");

        user_operation::create("board/sortTask","Сохранение сортировки задач")
            ->appendTo("board/editTask");

       user_operation::create("board/viewAllTasks","Просмотр всех задач")
            ->appendTo("boardUser");

       user_operation::create("board/listTaskAttachments","Получение списка вложений для задачи")
            ->appendTo("board/viewTask");

       user_operation::create("board/uploadFile","Закачивание файла в задачу")
            ->appendTo("board/editTask");

       user_operation::create("board/pauseTask","Приостановка задачи")
            ->addBusinessRule('if($task->data("status") != board_task_status::STATUS_IN_PROGRESS) $this->error("Нельзя поставить задачу на паузу в статусе {$task->status()->title()}");')
            ->addBusinessRule("return true;")
            ->appendTo("board/editTask");

       user_operation::create("board/updateTaskNotice","Изменение заметки")
            ->appendTo("boardUser"); // только для менеджеров

       user_operation::create("board/updateTaskTag","Изменение тэга")
            ->appendTo("boardUser");

        // Доступ

       user_operation::create("board/showAccessList","Просмотр списка доступов")
            ->appendTo("boardUser");

       user_operation::create("board/getAccessData","Получить данные доступа")
            ->appendTo("boardUser");

       user_operation::create("board/updateAccessData","Изменить данные доступа")
            ->appendTo("boardUser");

        // Пользователи

        user_operation::create("board/getUserList","Просмотр списка пользователей")
			->appendTo("boardUser");
            
		// Голосование
		
       user_operation::create("board/vote","Голосовать за задачу")
			->addBusinessRule('if($user->id()==$task->responsibleUser()->id() && $criteria->data("voter-self")) return true;')
			->addBusinessRule('if($user->id()!=$task->responsibleUser()->id() && $criteria->data("voter-other")) return true;')
			->addBusinessRule('return false;')
            ->appendTo("boardUser");

		// Отчеты

		user_operation::create("board/showReportUsers","Просмотр отчета по пользователям")
			->appendTo("boardUser");

		user_operation::create("board/showReportVote","Просмотр отчета по голосованиям")
			->appendTo("boardUser");

		user_operation::create("board/showReportGallery","Просмотр галереи")
			->appendTo("boardUser");

		user_operation::create("board/showAllUsersDailyActivity","Просмотр активности пользователей")
			->appendTo("boardUser");

		user_operation::create("board/showReportProjectActivity","Просмотр отчета активности по проекту")
			->appendTo("board/viewProject");

		user_operation::create("board/showProjectsReport","Просмотр отчета по пользователям")
		    ->appendTo("guest");

		user_operation::create("board/showReportDone","Просмотр отчета по сделанному")
		    ->appendTo("guest");
        
    }
    
    public function on_user_subscription_beforeMail($event) {
    
        mod::msg($event->param("subscriptionKey"));
    
        // Предотвращаем отправку писем о выполненных задачах самому себе
		if(preg_match("/^board\/project-(\d+)\/taskCompleted$/",$event->param("subscriptionKey"))) {
		    if($event->param("completedBy") == $event->param("userID")) {
		        $event->stop();
		    }
		}
    }

}
