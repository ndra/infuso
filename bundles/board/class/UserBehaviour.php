<?

namespace Infuso\Board;

/**
 * Поведение для класса пользователя, добавляющее методы доски
 **/
class userBehaviour extends \infuso\Core\Behaviour {

    public function boardLog() {
        return board_task_log::all()->eq("userID",$this->id());
    }
    
    public function boardLog24H() {
        return $this->boardLog()->gt("created",util::now()->shift(-24*3600));
    }
    
    public function boardLogWeek() {
        return $this->boardLog()->gt("created",util::now()->shift(-24*3600*7));
    }
    
    public function boardLogMonth() {
        return $this->boardLog()->gt("created",util::now()->shift(-24*3600*30));
    }
    
    public function completedTasks() {
        return board_task::all()->eq("status",3)->eq("responsibleUser",$this->id());
    }
    
}
