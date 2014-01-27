<?

namespace infuso\core;

abstract class file extends component {

    public static $fileClass = "mod_file";
    
    private static $temporaryFolder = "/mod/_temp/";
    
    private static $foldersToDelete = array();

    protected $path = null;
    
    /**
     * Признак существующего файла
     * Ваши методы, которые возвращают файл могут, использовать file::nonExistent()
     * для получения экземпляра несуществующего файла
     * Все обращения к этому файлу не будут отсылаться файловой системе
     **/
    protected $exists = true;
    
    /**
     * Возвращает полный путь к файлу
     **/
    public function path() {
        return $this->path;
    }
    

    public function __toString() {
        return $this->path()."";
    }
    
    /**
     * Уведомляет о начале операции с файлом (для профайлера)
     **/
    public static function beginOperation($operation,$name) {
        \infuso\core\profiler::beginOperation("file",$operation,$name);
    }

    /**
     * Уведомляет об окончании операции с файлом
     **/
    public static function endOperation() {
        \infuso\core\profiler::endOperation();
    }

    /**
     * Обертка для конструктора
     * @param $string Относительный путь к файлу от корня сайта
     **/
    public static function get($name) {
    
        $name.="";

        // В режиме отладки ведем лог
        self::beginOperation("get",$name);

        $ret = new localFile($name);

        // В режиме отладки ведем лог
        self::endOperation();

        return $ret;
    }
    
    public function http($path) {
    
        $path.="";

        // В режиме отладки ведем лог
        self::beginOperation("get",$path);

        $ret = new mod_file_http($path);

        // В режиме отладки ведем лог
        self::endOperation();

        return $ret;
    
    }
    
    /**
     * Создает временную парку и возвращает ее.
     * Временная папка будет уничтожена в конце работы скрипта
     **/
    public function tmp() {
        $chars = "1234567890qwertyuiopasdfghjklzxcvbnm";
        $id = "";
        for($i=0;$i<20;$i++) $id.= $chars[rand()%strlen($chars)];
        $path = self::$temporaryFolder."/$id/";
        file::mkdir($path,1);
        self::$foldersToDelete[] = $path;
        register_shutdown_function(array("mod_file","clearTemporaryFolders"));
        return self::get($path);
    }

    /**
     * Возвращает несуществующий файл
     **/
    public static function nonExistent() {
        $file = self::get("-1");
        $file->exists = false;
        return $file;
    }
    
    /**
     * @return bool Существует ли файл
     **/
    public function exists() {

        self::beginOperation("exists", $this);

        if(!$this->exists) {
            $ret = false;
        } else {
            $ret = file_exists($this->native());
        }

        $this->endOperation();

        return $ret;
    }
    
    /**
     * Перемещает заказанный файл в заданную папку
     * @param $string Имя файла в нативной файловой системе
     * @param $string Будущее имя файла в файловой системе цмс
     **/
    public static function moveUploaded($from,$to) {
        $to = self::get($to)->native();
        move_uploaded_file($from,$to);
    }
    
    public static function clearTemporaryFolders() {
        foreach(self::$foldersToDelete as $folder)
            file::get($folder)->delete(1);
    }
    
    /**
     * Создает папку
     **/
    public static function mkdir($name) {

        \infuso\core\profiler::beginOperation("file","mkdir",$name);

        $name = self::get($name)->path();
        $n2 = trim($name,"/");
        $n2 = explode("/",$n2);
        array_pop($n2);
        if(sizeof($n2)) {
            $n2 = join("/",$n2);
            self::mkdir($n2);
        }
        @mkdir(self::get($name)->native());

        \infuso\core\profiler::endOperation();

        return self::get($name);

    }
    
    public function folder() {
        return false;
    }
    
    /**
     * Копирует фал или папку в папку $dest
     **/
    public function copy($dest) {
        if($this->folder()) {
            self::mkdir($dest);
            foreach($this->dir() as $item) {
                $path = $dest."/".$item->rel($this->path());
                $item->copy($path);
            }
        } else {
            $old_name = $this->native();
            $new_name = self::get($dest)->native();
            @copy($old_name,$new_name);
        }
    }
    
    public function unzip($dest) {

        $zip = new ZipArchive;
        $res = $zip->open($this->native());
        
        if ($res === TRUE) {
            $zip->extractTo(mod_file::get($dest)->native());
            $zip->close();
        }
    }
    
    /**
     * Возвращает содержимое файла
     **/
    public final function data() {
        return $this->contents();
    }
    
    abstract function contents();
    abstract function native();
    
}
