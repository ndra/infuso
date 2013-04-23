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
        
        $o = user_operation::create("board:createProject","Создание проекта в доске");
        $o->appendTo("boardUser");
        
        // Операции с задачами

        $o = user_operation::create("board/editTask","Редактирование задачи")
            ->addBusinessRule('if(!$task->exists()) $this->error("Задача не существует"); ')
            ->addBusinessRule('return true;')
			->appendTo('boardUser');
        
        user_operation::create("board/updateTaskParams","Обновление полей задачи")
            ->appendTo("board/editTask");
        
        user_operation::create("board/getTaskParams","Получение полей задачи")
            ->appendTo("board/editTask");

        user_operation::create("board/getEpicSubtasks","Получение списка подзадач эпика")
            ->appendTo("board/editTask");

        user_operation::create("board/addEpicSubtask","Добавление подзадачи эпика")
            ->appendTo("board/editTask");

        user_operation::create("board/changeTaskStatus","Изменение статуса задачи")
            ->appendTo("board/editTask");

        user_operation::create("board/newTask","Создание задачи")
            ->appendTo("boardUser");

        user_operation::create("board/sortTasks","Сортировка задач")
            ->appendTo("boardUser");

       user_operation::create("board/viewAllTasks","Просмотр всех задач")
            ->appendTo("boardUser");

       user_operation::create("board/uploadFile","Закачивание файла в задачу")
            ->appendTo("board/editTask");
        
    }

}
