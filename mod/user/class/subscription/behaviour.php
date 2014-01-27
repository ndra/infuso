<?

class user_subscription_behaviour extends mod_behaviour {

    /**
     * Прикрепить поведение классу user
     **/
    public function addToClass() {
        return "user";
    }
    
    /**
     * Возвращает подписки данного пользователя
     **/
    public function subscriptions() {
        return user_subscription::all()->eq("userID",$this->id());
    }
    
    public function reflex_children() {
        return array(
            $this->subscriptions()->title("Подписки"),
        );
    }

    /**
     * Подписывает пользователя
     **/
    public function subscribe($key,$title) {

        if(!$this->exists()) {
            throw new Exception("Нельзя подписаться, т.к. вход не выполнен");
        }

		if(!$this->subscriptions()->eq("key",$key)->one()->exists()){
			reflex::create("user_subscription",array(
				"userID" => $this->id(),
				"key" => $key,
				"title" => $title,
			));
		}	
    }
    
}
