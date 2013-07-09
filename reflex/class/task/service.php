<?

class reflex_task_service extends mod_service {

 
    public function defaultService() {
        return "task";
    }
    
    public function initialParams() {
        return array(
            "timeout" => 2,
        );
    } 
    
     /**
     * Возвращает список задач, которые уже могут быть выполнены
     **/
    public function tasksToLaunch() {
        return reflex_task::all()
            ->leq("nextLaunch",util::now())
            ->eq("completed",0);
    }
    
    
    /**
     * Выполняет одно задание
     **/
    public function execOne() {

        $tasks = $this->tasksToLaunch();
        $total = $tasks->count();

        if($total==0) {
            return;
        }

        // $n - хранится в кэше и увеличивается на 1 с каждым запуском крона
        $n = mod_cache::get("01h1b4yw6kbz2l9y6orj");
        if(!$n) {
            $n = 0;
        }

        // Выбираем задачу в зависимости от $n
        // Т.о. каждый на запуск крона задачи будут поочередно вызываны
        $task = $tasks->limit(1)->page($n%$total+1)->one();

        mod_cache::set("01h1b4yw6kbz2l9y6orj",$n+1);

        $task->exec();
    }
    
    
     public function runTasks() {
        $start = microtime(true);
        while(microtime(true) - $start < $this->param("timeout")) {
            $this->execOne();
            reflex::storeAll();
        }        
    }
    
}     
