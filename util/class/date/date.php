<?

class util_date extends mod_component {


    private $format = "d.m.y H:i:s";
    private $time = null;

    private $notime = null;
    private $noyear = null;
    
    /**
     * Возвращает текущую дату
     **/
    public static function now() {
        return self::get(@date("Y-m-d H:i:s"));
    }

    public static function get($y,$m=1,$d=1,$h=0,$min=0,$s=0) {
        switch(func_num_args()) {
            case 1:
                return new self(func_get_arg(0));
                break;
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                return new self(mktime($h,$min,$s,$m,$d,$y));
                break;
        }

    }

    /**
     * Конструктор
     **/
    private function __construct($time,$m=1,$d=1,$h=0,$min=0,$s=0) {
    
        switch(func_num_args()) {
            case 1:
		        // Числа интерпретируются как timestamp
		        if(intval($time).""==$time) {
		            $this->time = intval($time);

		        // В противном случае попробуем распарсить строку
		        } else {
		            $this->time = @strtotime($time);
		        }
                break;
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                $this->time = mktime($h,$min,$s,$m,$d,$time);
                break;
        }


    }

    /**
     * Объект преобразуется в строку c mysql-представлением даты, например 2038-01-19 03:14:07
     */
    public function __toString() {
        return $this->standart();
    }
    
    public function copy() {
        return clone $this;
    }

    /**
     * Возвращает текстовое значение времни
     * Например, 12 февраля 2012 г. 12:35
     **/
    public function text() {

        $ret = "";
        $date = @getdate($this->time);

        $d = clone $this;


        if($d->notime()->num()==util::now()->notime()->num()) {
            $ret.= "сегодня ";
        } elseif($d->notime()->num()==util::now()->shiftDay(-1)->notime()->num()) {
            $ret.= "вчера ";
        } else {

            $months = array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
            $ret.= $date["mday"]." ".$months[$date["mon"]-1];

            if(!$this->noyear) {
                $ret.= " $date[year] г. ";
            }
        }

        // Добавляем время
        if(!$this->notime)
            $ret.= $date["hours"].":".str_pad($date["minutes"],2,0,STR_PAD_LEFT);
        return $ret;
    }

    public function txt() {
        return $this->text();
    }

    /**
     * Возвращает числовое значение времени, например 30.12.20012
     **/
    public function num() {
        return @date($this->format,$this->stamp());
    }

    /**
     * Оставляет только дату
     * Убираем часы, минуты и секунды из таймстэмпа
     **/
    public function date() {
        $this->notime = 1;
        $this->format("d.m.y");
        $this->time = strtotime(date("Y-m-d", $this->time));
        return $this;
    }

    /**
     * Алиас к util_date::date()
     **/
    public function notime() {
        return $this->date();
    }

    public function noyear() {
        $this->noyear = 1;
        return $this;
    }

    public function time() {
        $this->notime = 1;
        $this->format("H:i:s");
        return $this->num();
    }

    public function format($format) {
        $this->format = $format;
        return $this;
    }

    /**
     * Возвращет метку времени linux
     **/
    public function stamp() {
        return $this->time;
    }

    /**
     * Прибавляет к дате заданное количество секунд (переносит дату в будущее)
     * Если аргумент отрицательный, переносит дату на заданное количество секунд в прошлое
     **/
    public function shift($s) {
        $this->time += $s;
        return $this;
    }

    /**
     * Увеличивает / уменьшает дату на заданное количество дней
     **/
    public function shiftDay($day) {

        $day = intval($day);
        if(!$day) {
            return $this;
        }

        $this->time = strtotime("+{$day} day",$this->time);
        return $this;
    }

    /**
     * Увеличивает / уменьшает дату на заданное количество месяцев
     **/
    public function shiftMonth($m) {

        $m = intval($m);
        if(!$m) {
            return $this;
        }

        $this->time = strtotime("+{$m} month",$this->time);
        return $this;
    }

    /**
     * Увеличивает / уменьшает дату на заданное количество лет
     **/
    public function shiftYear($year) {

        $year = intval($year);
        if(!$year) {
            return $this;
        }

        $this->time = strtotime("+{$year} year",$this->time);
        return $this;
    }

    public function standart() {
        return @date($this->notime ? "Y-m-d" : "Y-m-d H:i:s", $this->stamp());
    }

    public function left() {
        $d = time()-$this->stamp();
        $ret = "";
        $ret.= self::duration($d);
        $ret = $d>0 ? "$ret назад" : "через $ret";
        return $ret;
    }

    public static function duration($d) {
        $d = abs($d);
        $minutes = floor($d/60);
        $hours = floor($minutes/60);
        $days = floor($hours/24);
        $minutes%=60;
        $hours%=24;
        $ret = "";
        if($days) $ret.= "$days д. ";
        if(($days>0 && $days<4)||($days==0&$hours>0)) $ret.= "$hours ч. ";
        if(!$days && $hours<3) $ret.= "$minutes мин. ";
        return trim($ret);
    }

    /**
    * Возвращает год
    **/
    public function year() {
        return @date("Y",$this->stamp());
    }

    /**
    * Возвращает месяц
    **/
    public function month() {
        return @date("m",$this->stamp());
    }

    /**
    * Возвращает день
    **/
    public function day($day = null) {
    
        if(func_num_args() == 0 ) {
        	return @date("j",$this->stamp());
		}
		
		if(func_num_args() == 1 ) {
        	$date = new self($this->year(),$this->month(),$day,$this->hours(),$this->minutes(),$this->seconds());
        	$this->time = $date->stamp();
        	return $this;
		}
    }
    
    /**
     * Возвращает номер коммерческого дня недели
     * 1 - Понедельник
     * 2 - Вторник
     * ...
     * 7 - Воскресенье
     **/
    public function commercialWeekDay($day = null) {
    
        if(func_num_args() == 0 ) {
        
			$map = array(
				0 => 7,
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 3,
				5 => 5,
				6 => 6,
			);
        
        	return $map[date("w",$this->stamp())];
        }
        
		if(func_num_args() == 1 ) {
			$wday = $this->commercialWeekDay();
			$this->shiftDay( 1 - $wday );
			$this->shiftDay( $day - 1 );
			return $this;
		}
        
    }

    /**
     * Возвращает / изменяет секунды даты
     **/
    public function seconds($sec=null) {

        if(func_num_args()==0) {
            return @date("s",$this->stamp());
        }

        if(func_num_args()==1) {
            $s = @date("s",$this->stamp());
            $this->time += ($sec - $s);
            return $this;
        }

    }

    /**
     * Возвращает / изменяет минуты даты
     **/
    public function minutes($min=null) {

        if(func_num_args()==0) {
            return @date("i",$this->stamp());
        }

        if(func_num_args()==1) {
            $m = @date("i",$this->stamp());
            $this->time += ($min - $m)*60;
            return $this;
        }

    }

    /**
     * Возвращает / изменяет часы даты
     **/
    public function hours($hours=null) {

        if(func_num_args()==0) {
            return @date("H",$this->stamp());
        }

        if(func_num_args()==1) {
            $h = @date("H",$this->stamp());
            $this->time += ($hours - $h)*3600;
            return $this;
        }

    }

    /**
    * Возвращает месяц в текстовом виде
    **/
    public function monthTxt() {
        $m = array(
            "январь",
            "февраль",
            "март",
            "апрель",
            "май",
            "июнь",
            "июль",
            "август",
            "сентябрь",
            "октябрь",
            "ноябрь",
            "декабрь",
        );
        return $m[$this->month()-1];
    }

    /**
    * Возвращает фазу луны
    **/
    public function moonPhase() {
        $ret = date_moon::phase($this->stamp());
        var_export($ret);
    }

}
