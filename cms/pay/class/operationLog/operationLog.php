<?

/**
 * Запись совершения операции с внутренним счетом пользователя (списание/зачисление)
 **/
class pay_operationLog extends reflex {

    /**
     * Взять все записи
     **/
    public static function all() {
        return reflex::get(get_class())->desc("date");
    }
    
    /**
     * Взять или установить значение даты операции
     **/
    public function date($date = null) {
        if ($date == null) {
            return $this->data("date"); }
        else {
            $this->data("date", $date);
            return $this; }
    }
    
    /**
     * Возвращает сумму транзакции
     **/
    public function amount() {
        return $this->data("amount");
    }
    
    /**
     * Взять или установить значения комментария к совершенной операции
     **/
    public function comment($comment = null) {
        if ($comment == null) {
            return $this->data("comment");
        } else {
            $this->data("comment", $comment);
            return $this;
        }
    }
    
    /**
     * Отобразить форматированные заголовки в списке подменю "Операции"
     **/
    public function reflex_title() {

        $ret = $type." ".$this->amount()." ".$this->currency()." от ".$this->date()." ".$this->comment();
        
        if ($this->amount() >= 0) {
            $ret = "<span style='color:green;'>$ret</span>";
        } else {
            $ret = "<span style='color:red;'>$ret</span>";
        }
        
        return $ret;
        
    }
    
    public function user() {
        return $this->pdata("userId");
    }
    
    public function reflex_parent() {
        return $this->user();
    }
    
    /**
     * Заполнить данные по умолчанию только что созданого элемента
     **/
    public function reflex_beforeCreate() {
        $this->date(util::now());
    }
    
}
