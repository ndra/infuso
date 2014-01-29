<?

/**
 * Класс-генератор превьюшек
 **/
class file_preview extends mod_component {

    private $src = null;
    private $cache = true;

    private $img;

    /**
     * Свойство, содержащее код ошибки, которая возникла при обработке картинки
     **/
    private $error = false;

    private $transparent = false;

    /**
     * Ширина картинки на текущий момент
     **/
    private $width = 0;

    /**
     * Высота картинки на текущий момент
     **/
    private $height = 0;
    
    /**
     * Коммпресия jpeg в %
     **/
    private $jpegCompression = 100;
    
    /**
     * Стэк операций
     **/
    private $stack = array();

    const ERROR_NOT_FOUND = 1;
    const ERROR_TOO_LARGE = 2;
    const ERROR_LOAD_FAILED = 3;

    public function __construct($src=null,$width=100,$height=100) {

        $this->src = file::get($src)->path();

        if(func_num_args()==3) {
            if($width)
                $this->width($width);
            if($height)
                $this->height($height);
        }
    }

    /**
     * Параметры по умолчанию для компонента
     * @todo сделать настрйоку фона превьюшек
     **/
    public function initialParams() {
        return array(
            "maxWidth" => 2000, // Максимальная ширина превьюшки
            "maxHeight" => 2000, // Максимальная высота превьюшки
            "background" => "",
            "jpegCompression" => 100
        );
    }

	public function confDescription() {
	    return array(
	        "components" => array(
	            "file_preview" => array(
	                "params" => array(
	                    "maxWidth" => "Максимальная ширина превьюшки",
	                    "maxHeight" => "Максимальная высота превьюшки",
	                    "background" => "Цвет фона",
	                    "jpegCompression" => "Уровень компресии jpg (0 — 100)",
					),
				),
			),
		);
	}

    private function raiseError($error) {
        $this->error = $error;
    }

    private function getWidth() {
        return min($this->param("maxWidth"),$this->width);
    }

    private function getHeight() {
        return min($this->param("maxHeight"),$this->height);
    }
    
    private function getJpegCompression() {
        return min($this->param("jpegCompression"),$this->jpegCompression);
    }

    /**
     * Добавляет операцию изменения размера
     **/
    public function preview($width,$height) {

        if($width) {
            $this->width($width);
        }

        if($height) {
            $this->height($height);
        }

        return this;
    }

    /**
     * Загружает исходное изображение
     **/
    private function loadSrcImage() {
        $this->img = $this->loadImage($this->src);
    }

    /**
     * Возвращает ресурс изображения, с которым производятся манипуляции
     **/
    private function img() {
        return $this->img;
    }

    /**
     * Произошла ли ошиюка при построении превьюшки?
     **/
    private function isError() {
        return $this->error == true;
    }
    
    private function getError() {
        return $this->error;
    }

    /**
     * Добавляет операцию в стэк
     **/
    public function addOperation($op) {

        $flag = true;
        // Если передан параметр combine, исстема пытается найти в списке операций
        // операцию с таким же именем и добавляет к ней параметры
        if($op["combine"]) {
            $stackLastKey = sizeof($this->stack)-1;
            if($this->stack[$stackLastKey]["name"]==$op["name"]) {

                foreach($op as $key=>$val) {
                    $this->stack[$stackLastKey][$key]=$val;
                }

                $flag = false;
            }
        }

        if($flag) {
            $this->stack[] = $op;
        }
    }

    /**
     * Обрабатывает стэк операций
     **/
    public function render() {

        mod_profiler::beginOperation("file","renderPreview",$this->src);

        $this->loadSrcImage();

        foreach($this->stack as $op) {

            // Если случилась ошиюка, просто регистрируем изменение ширины и высоты
            // для того чтобы в конце сгенерировать красивую картинку с ошибкой
            if($op["width"]) {
                $this->width = $op["width"];
            }

            if($op["height"]) {
                $this->height = $op["height"];
            }

            // Если нет ошибки, выполняем стэк обычном режиме
            if(!$this->isError()) {
                call_user_func(array($this,"operation_".$op["name"]),$op);
            }
        }

        mod_profiler::endOperation();

        return $this;

    }

    /**
     * Возвращает хэш стэка операций
     * Используеттся для того чтобы опередить уникальный путь к файлу превьюшки в ФС
     **/
    public function getStackHash() {
        return md5(serialize($this->stack).":".$this->src);
    }

    private function isTransparent() {
        return $this->transparent;
    }

    public function transparent($t=true) {
        $this->transparent = $t;
        return $this;
    }

    public function save($dest) {
        
        if($this->isError()) {
        
            $bundlePath = self::inspector()->bundle()->path();

            switch($this->getError()) {
                case self::ERROR_TOO_LARGE:
                    $error = file::get("{$bundlePath}/noimage/error.png")->preview($this->getWidth(),$this->getHeight());
                    $error->render()->save($dest);
                    return;
                default:
                    $error = file::get("{$bundlePath}/noimage/noimage.png")->preview($this->getWidth(),$this->getHeight());
                    $error->render()->save($dest);
                    return;
            }
        }

        // Сохранение с прозрачностью в png
        if($this->isTransparent()) {
            imagesavealpha($this->img(),true);
            imagepng($this->img(),file::get($dest)->native());

        // Сохранение без прозрачности в jpg
        } else {

            // Если исходное изображение было с прозрачными областями,
            // То при сохранении в jpg на месте полупрозрачности появится черный мусор
            // Поэтому мы создаем непрозрачную подложку и копируем изображение сверху
            $width = imagesx($this->img());
            $height = imagesy($this->img());
            $img = imagecreatetruecolor($width,$height);
            $bgcolor = self::hex2RGB($this->param("background"));
            $color = imagecolorallocate($img,$bgcolor["red"],$bgcolor["green"],$bgcolor["blue"]);
            imagefill($img,0,0,$color);
            imagecopy($img,$this->img(),0,0,0,0,$width,$height);
            imagejpeg($img,file::get($dest)->native(),$this->getJpegCompression());
        }
    }

    /**
     * Выключает кэширование данного изображения
     **/
    public function nocache() {
        $this->cache = false;
        return $this;
    }

    /**
     * Алиас к get()
     **/
    public function __toString() {
        return $this->get();
    }

    /**
     * Выполняет все операции их стэка и сохраняет файл
     * Возвращает строку, содержащую имя файла превьюшки
     **/
    public function get() {
    
        mod_profiler::beginOperation("file","preview",$this->src);

        $dest = $this->destFilename();

        // Если включено кэширование и файл существует, возвращаем путь этого файла
        if($this->cache && file::get($dest)->exists()) {
            mod_profiler::endOperation();
            return $dest;
        }

        $this->render();
        $this->prepareDir();
        $this->save($dest);
        mod_profiler::endOperation();
        return $dest;
    }

    /**
     * Создает директорию для выходного файла
     **/
    public function prepareDir() {
        $destDir = explode("/",$this->destFilename());
        array_pop($destDir);
        $destDir = implode("/",$destDir);
        file::mkdir($destDir);
    }

    /**
     * Возвращает имя целевого файла
     * Имя генерируется на основе хэша от списка операций
     * Чтобы не перегружать файловую систему, файлы распределяются по разным папкам
     * Имя папки - первые две буквы хэша
     **/
    public function destFilename() {
        $hash = $this->getStackHash();
        $group = substr($hash,0,2);
        $time = @filemtime(file::get($this->src)->native());
        $ext = $this->isTransparent() ? "png" : "jpg";
        
        return mod::app()->publicPath()."/preview/$group/{$time}_$hash.$ext";
    }

    /**
     * Создает изображение с такими же размерами как у текущего
     **/
    private function newImage() {
        $width = imagesx($this->img());
        $height = imagesy($this->img());
        $img = imagecreatetruecolor($width,$height);
        $color = imagecolorallocatealpha($img,255,255,255,127);
        imagefill($img,0,0,$color);
        return $img;
    }

    /**
     * Загружает изображение
     * return resource $image
     **/
    private function loadImage($src) {

        // Если файл не указан, не генерируем превьюшку
        if(!trim($src," /.")) {
            $this->raiseError(self::ERROR_NOT_FOUND);
            return;
        }

        // Проверяем размеры исходной картинки, чтобы избежать нехватки памяти
        $size = @getimagesize(file::get($src)->native());
        $width = $size[0];
        $height = $size[1];

        if($width > $this->param("maxWidth") || $height > $this->param("maxHeight")) {
            $this->raiseError(self::ERROR_TOO_LARGE);
            return;
        }

        // Загружаем картинку
        switch(file::get($src)->imageType()) {
            case "jpg":
                $img = @imagecreatefromjpeg(file::get($src)->native());
                break;
            case "gif":
                $img = @imagecreatefromgif(file::get($src)->native());
                break;
            case "png":
                $img = @imagecreatefrompng(file::get($src)->native());
                break;
        }
        
        if(!$img) {
            $this->raiseError(self::ERROR_LOAD_FAILED);
        }

        return $img;
    }

    private static function hex2RGB($hex) {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hex); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            $rgbArray['red'] = 255;
            $rgbArray['green'] = 255;
            $rgbArray['blue'] = 255;
        }
        return $rgbArray;
    }
    
    public function valign($valign) {
        $available = array("top","middle","bottom");
        if(!in_array($valign,$available)){
            throw new Exception("Undefined vertical-align type");   
        }
        $this->setResizeParam("valign", $valign);
        return $this;
    }
    
    private function setResizeParam($key,$val) {
        $this->addOperation(array(
            "name" => "resize",
            $key => $val,
            "combine" => true,
        ));
    }

    /**
     * Задает ширину превьюшки
     **/
    public function width($width) {
        $width = intval($width);

        if($width<16)
            $width = 16;

        if($width > $this->param("maxWidth")) {
            $width = $this->param("maxWidth");
        }

        $this->setResizeParam("width",$width);
        return $this;
    }

    /**
     * Задает высоту превьюшки
     **/
    public function height($height) {
        $height = intval($height);

        if($height<16) {
            $height=16;
        }

        if($height > $this->param("maxHeight")) {
            $height = $this->param("maxHeight");
        }

        $this->setResizeParam("height",$height);
        return $this;
    }

    /**
     * Разрешает увеличивать маленькие изображения при генерации превьюшек
     **/
    public function maximize() {
        $this->setResizeParam("maximize",true);
        return $this;
    }

    public function fit($p=1) {
        $this->setResizeParam("mode","fit");
        return $this;
    }

    /**
    * Устанавливает режим генерации превьюшки - crop;
    * Если вызвана с двумя параметрами ($width,$height) - дополнительно обрезает картинку
    **/
    public function crop($width=null,$height=null,$c=null,$d=null) {

        if(func_num_args()==0) {
            $this->setResizeParam("mode","crop");
            return $this;
        }

        if(func_num_args()==2) {
            $this->canvasSize($width,$height);
            return $this;
        }

        if(func_num_args()==4) {
            $this->canvasSize($width,$height,$c,$d);
            return $this;
        }

    }

    public function resize($width=null,$height=null) {
        if(func_num_args()==0) {
            $this->setResizeParam("mode","resize");
            return $this;
        }
    }

    private function operation_resize($p) {

        $srcWidth = imagesx($this->img());
        $srcHeight = imagesy($this->img());
        $ratio = $srcWidth/$srcHeight;

        $previewWidth = $p["width"];
        $previewHeight = $p["height"];

        $reduce = $previewWidth<$srcWidth || $previewHeight < $srcHeight || $p["maximize"];

        $mode = $p["mode"];

        if($this->isError()) {
            $reduce = false;
            $mode = "crop";
        }

        $scale1 = $previewWidth/$srcWidth;
        $scale2 = $previewHeight/$srcHeight;
        $scale = $scale1<$scale2 ? $scale1 : $scale2;

        switch($mode) {
            default:
            case "fit":
                if($reduce) {
                    $dw = $srcWidth*$scale;
                    $dh = $srcHeight*$scale;
                } else {
                    $dw = $srcWidth;
                    $dh = $srcHeight;
                }
                break;
            case "resize":
                if($reduce) {
                    $dw = $srcWidth*$scale;
                    $dh = $srcHeight*$scale;
                } else {
                    $dw = $srcWidth;
                    $dh = $srcHeight;
                }
                $previewWidth = $dw;
                $previewHeight = $dh;
                break;
            case "crop":
                $scale = $scale1>$scale2 ? $scale1 : $scale2;
                if($reduce) {
                    $dw = $srcWidth*$scale;
                    $dh = $srcHeight*$scale;
                } else {
                    $dw = $srcWidth;
                    $dh = $srcHeight;
                }
                break;
        }

        // Если картинка очень узкая, может получиться 0 по одному из изменений
        // Не допускаем этого
        if($previewWidth < 1)
            $previewWidth = 1;
        if($previewHeight < 1)
            $previewHeight = 1;

        // Создаем картинку для будущей превьюшки
        // и заливаем ее фоном
        $destImg = @imagecreatetruecolor($previewWidth,$previewHeight);
        imagealphablending($destImg,true);
        imagesavealpha($destImg,true);
        $color = imagecolorallocatealpha($destImg,255,255,255,127);
        imagefill($destImg,0,0,$color);

        $y = ($previewHeight-$dh)/2;
        if($p["mode"]=="crop"){
            switch($p["valign"]) {
                default:
                    $y = 0;
                    break;
                case "top":
                    $y = 0;
                    break;
                case "middle":
                    $y = ($previewHeight-$dh)/2;
                    break;
                case "bottom":
                    $y = $previewHeight-$dh;
                    break;                  
            }
        }    

        @imagecopyresampled($destImg,$this->img(),($previewWidth-$dw)/2,$y,0,0,$dw,$dh,$srcWidth,$srcHeight);
        $this->img = $destImg;
    }

    /**
    * Изменение размера холста без растяжения картинки (обрезка)
    * Два параметра: ширина и высота
    * Четыре параметра: x1,y1,x2,y2
    **/
    public function canvasSize($a,$b,$c=null,$d=null) {

        if(func_num_args()==2) {
            $left = 0;
            $top = 0;
            $width = $a;
            $height = $b;
        }

        if(func_num_args()==4) {
            $left = $a;
            $top = $b;
            $width = $c-$a;
            $height = $d-$b;
        }

        $this->addOperation(array(
            "name" => "canvasSize",
            "left" => $left,
            "top" => $top,
            "width" => $width,
            "height" => $height,
        ));

        return $this;
    }

    /**
    * Изменение размера холста без растяжения картинки (обрезка)
    **/
    private function operation_canvasSize($p) {

        // Создаем картинку для будущей превьюшки
        // и заливаем ее фоном

        $destImg = @imagecreatetruecolor($p["width"],$p["height"]);
        imagealphablending($destImg,true);
        imagesavealpha($destImg,true);
        $color = imagecolorallocatealpha($destImg,255,255,255,127);
        imagefill($destImg,0,0,$color);

        $srcWidth = imagesx($this->img());
        $srcHeight = imagesy($this->img());
        @imagecopy($destImg,$this->img(),0,0,$p["left"],$p["top"],$srcWidth,$srcHeight);
        $this->img = $destImg;
    }

    /**
     * Добавляет в стэк операцию обесцвечивания изображения
     **/
    public function desaturate() {
        $this->addOperation(array(
            "name" => "desaturate",
        ));
        return $this;
    }

    /**
     * Выполняет операцию обесцвечивания
     **/
    private function operation_desaturate() {
        $width = imagesx($this->img());
        $height = imagesy($this->img());
        for($x=0;$x<$width;$x++)
        for($y=0;$y<$height;$y++) {
            $rgba = imagecolorat($this->img(),$x,$y);
            $rgba = imagecolorsforindex($this->img(),$rgba);
            $k = floor(($rgba["red"]+$rgba["green"]+$rgba["blue"])/3);
            $index = imagecolorallocatealpha($this->img(),$k,$k,$k,$rgba["alpha"]);
            imagesetpixel($this->img(),$x,$y,$index);
        }
    }

    /**
     * Добавляет в стэк операцию наложения маски
     * @param Путь к файлу маски
     **/
    public function mask($mask) {
        $this->transparent();
        $this->addOperation(array(
            "name" => "mask",
            "mask" => $mask,
        ));
        return $this;
    }

    private function operation_mask($op) {

        $maskfile = file::get($op["mask"]);
        switch($maskfile->imageType()) {
            case "jpg":
                $mask = @imagecreatefromjpeg($maskfile->native());
                break;
            case "gif":
                $mask = @imagecreatefromgif($maskfile->native());
                break;
            case "png":
                $mask = @imagecreatefrompng($maskfile->native());
                break;
            default:
        }
        if(!$mask)
            return;

        $img = $this->newImage();

        $width = imagesx($this->img());
        $height = imagesy($this->img());
        for($x=0;$x<$width;$x++)
        for($y=0;$y<$height;$y++) {
            $rgba = imagecolorat($this->img(),$x,$y);
            $rgba = imagecolorsforindex($this->img(),$rgba);

            $mrgba = imagecolorat($mask,$x,$y);
            $mrgba = imagecolorsforindex($mask,$mrgba);

            // Прозрачность исходной картинки
            $a1 = (127-$rgba["alpha"])/127;
            // Прозрачность маски
            $a2 = ($mrgba["red"] + $mrgba["green"] + $mrgba["blue"])/3/255;
            // Результирующая прозрачность
            $a = $a1*$a2;
            $a = 127 - $a*127;
            $index = imagecolorallocatealpha($img,$rgba["red"],$rgba["green"],$rgba["blue"],$a);
            imagesetpixel($img,$x,$y,$index);
        }

        $this->img = $img;
    }

    public function watermark() {
        $this->addOperation(array(
            "name" => "watermark",
        ));
        return $this;
    }

    public function operation_watermark($p) {

        $watermark = $this->loadImage(mod::conf("file:watermark"));
        
        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);
        $previewWidth = imagesx($this->img());
        $previewHeight = imagesy($this->img());
        $margin = mod::conf("file:watermark-margin");

        switch(mod::conf("file:watermark-position")) {
            case "top-left":
                $dest_x = 0 + $margin;
                $dest_y = 0 + $margin;
                break;
            case "top-right":
                $dest_x = $previewWidth - $watermark_width - $margin;
                $dest_y = 0 + $margin;
                break;
            case "bottom-left":
                $dest_x = 0 + $margin;
                $dest_y = $previewHeight - $watermark_height - $margin;
                break;
            case "bottom-right":
                $dest_x = $previewWidth - $watermark_width - $margin;
                $dest_y = $previewHeight - $watermark_height - $margin;
                break;
            case "center":
                $dest_x = $previewWidth/2 - $watermark_width/2;
                $dest_y = $previewHeight/2 - $watermark_height/2;
                break;
        }

        imagecopy($this->img(), $watermark,$dest_x,$dest_y, 0, 0, $watermark_width, $watermark_height);

    }

    /**
    * Вставляет картинку поверх текущего изображения
    **/
    public function place($file,$x,$y) {
        $this->addOperation(array(
            "name" => "place",
            "file" => $file,
            "x" => $x,
            "y" => $y,
        ));
        return $this;
    }

    /**
    * Вставляет картинку поверх текущего изображения
    **/
    public function operation_place($p) {
        $img = $this->loadImage($p["file"]);
        imagecopy($this->img(),$img,0,0,$p["x"],$p["y"],imagesx($img),imagesy($img));
    }

    /**
    * Вращает исходную картинку
    **/
    public function rotate($angle) {
        $this->addOperation(array(
            "name" => "rotate",
            "angle" => $angle,
        ));
        return $this;
    }

    public function operation_rotate($p) {
        $this->img = imagerotate($this->img,$p["angle"],0);
    }

}
