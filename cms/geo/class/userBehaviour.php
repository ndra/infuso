<?

/**
 * Поведение, добавляющее гео-функции пользователю
 **/
class geo_userBehaviour extends mod_behaviour {

    public function behaviourPriority() {
        return -10;
    }

    /**
     * Добавляем к пользователю еще один метод определения города:
     * Если пользователь активен - определяем город по его ip
     **/
    public function city() {
        if($this->isActiveUser()) {
            return geo_ip::current()->city()->title();
        }
    }

    public function region() {
        if($this->isActiveUser()) {
            return geo_ip::current()->city()->region()->title();
        }
    }

    public function addToClass() {
        return "user";
    }

    /**
     * Возвращает объект города, для этого пользователя
     **/
    public function cityObject() {
        $city = geo_city::byName($this->component()->city());
        return $city;
    }
    
    /**
     * Возвращает объект города, для этого пользователя
     **/
    public function regionObject() {
        $city = geo_region::byName($this->component()->region());
        return $city;
    }

    /**
     * Возвращает "Домашние координаты" для пользователя
     **/
    public function homeCoordinates() {

        // Пробуем взять координаты из объекта города пользователя
        $cityCoords = $this->component()->cityObject()->coords();
        if($cityCoords->value()) {
            return $cityCoords;
        }

        // Если пользователь $thid - активный (user::active()), берем координаты из текущего ip
        if($this->isActiveUser()) {
            return geo_ip::current()->city()->coords();
        }

        // Если ничего не сработало, возвращаем пустую точку (0,0)
        return mod::field("point");

    }

}
