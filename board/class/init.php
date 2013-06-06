<?

class board_init implements mod_handler {

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

        user_operation::create("board/viewProjectList","Создание проекта в доске")
            ->appendTo("boardUser");
        
        // Операции с задачами

        $o = user_operation::create("board/editTask","Редактирование задачи")
            ->addBusinessRule('if(!$task->exists()) $this->error("Задача не существует"); ')
            ->addBusinessRule('return true;')
			->appendTo('boardUser');
        
        user_operation::create("board/updateTaskParams","Обновление полей задачи")
            ->appendTo("board/editTask");
        
        user_operation::create("board/getTaskParams","Получение полей задачи")
            ->appendTo("board/editTask");

        user_operation::create("board/getTaskTime","Получение времени, потраченного на задачу")
            ->appendTo("board/editTask");

        user_operation::create("board/getEpicSubtasks","Получение списка подзадач эпика")
            ->appendTo("board/editTask");

        user_operation::create("board/addEpicSubtask","Добавление подзадачи эпика")
            ->appendTo("board/editTask");

        user_operation::create("board/changeTaskStatus","Изменение статуса задачи")
            ->addBusinessRule("if(\$status == board_task_status::STATUS_IN_PROGRESS && !\$task->subtasks()->void()) \$this->error('Нельзя взять эпик. Возьмите подзадачу.'); ")
            ->addBusinessRule("return true;")
            ->appendTo("board/editTask");

        user_operation::create("board/newTask","Создание задачи")
            ->appendTo("boardUser");
            
        user_operation::create("board/newHindrance","Создание помехи")
            ->appendTo("boardUser");

        user_operation::create("board/sortTasks","Сортировка задач")
            ->appendTo("boardUser");

       user_operation::create("board/viewAllTasks","Просмотр всех задач")
            ->appendTo("boardUser");

       user_operation::create("board/listTaskAttachments","Получение списка вложений для задачи")
            ->appendTo("board/editTask");

       user_operation::create("board/uploadFile","Закачивание файла в задачу")
            ->appendTo("board/editTask");

       user_operation::create("board/pauseTask","Приостановка задачи")
            ->addBusinessRule('if($task->data("status") != board_task_status::STATUS_IN_PROGRESS) $this->error("Нельзя поставить задачу на паузу в статусе {$task->status()->title()}");')
            ->addBusinessRule("return true;")
            ->appendTo("board/editTask");

       user_operation::create("board/updateTaskNotice","Изменение заметки")
            ->appendTo("board/editTask");

       // Отчеты

       user_operation::create("board/showUserReport","Просмотр отчета по пользователям")
            ->appendTo("boardUser");
            
       user_operation::create("board/showProjectsReport","Просмотр отчета по пользователям")
            ->appendTo("boardUser");

       user_operation::create("board/showReportDone","Просмотр отчета по сделанному")
            ->appendTo("boardUser");
        
    }

}
