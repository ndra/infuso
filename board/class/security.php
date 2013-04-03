<?

class board_security {

	public function test($action,$p=array()) {
		switch($action) {

		    // Просмотр отчета потраченного времени
		    case "board:showReportDone":
		        return user::active()->check("board:access");
		        break;

			// Просмотр отчета параписки
		    case "board:showReportLog":
		        //return user::active()->check("board:access");
		        break;

			// Написание сообщений
		    case "board:sendMessage":
		        return user::active()->check("board:access");
		        break;

			// Создание проекта
		    case "board:createProject":
		        return user::active()->check("board:access");
		        break;

			// Редактирование проекта
		    case "board:updateProject":
		        return user::active()->check("board:access");
		        break;

			// Изменение текста задачи
		    case "board:updateTaskText":
		        if(user::active()->check("board:access"))
		            return true;
		        $customer = $p["task"]->project()->customer();
				if(user::active()->exists() && user::active()->id()==$customer->id())
				    return true;
				return false;

			// Изменение параметров задачи
		    case "board:updateTaskParams":
		        return user::active()->check("board:access");
		        break;

			// Изменение статуса задачи
		    case "board:changeTaskStatus":

		        if(user::active()->check("board:access")) return true;

		        // Если у задачи статус 10 (от клиента), то клиент может менять статус на любой другой
		        if($p["task"]->status()->id()==10) {
			        $customer = $p["task"]->project()->customer();
					if(user::active()->exists() && user::active()->id()==$customer->id())
					    return true;
				}

		        break;

			// Изменение приоритета задачи
			case "board:changeTaskPriority":
			    if(user::active()->check("board:access"))
					return true;
				break;

			// Закачка файлов
		    case "board:upload":
		        //return user::active()->check("board:access");
		        break;

		}
		return false;
	}

}
