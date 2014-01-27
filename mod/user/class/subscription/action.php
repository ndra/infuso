<?

/**
 * Контроллер управления подписками
 **/
class user_subscription_action extends mod_controller {

    public function indexTest() {
        return true;
    }

    public function index_list() {
        tmp::exec("user:subscription.list");
    }
   
   public function postTest() {
        return true;
   }
      
   /*удаляет выбраную подписку*/ 
   public function post_unsubscribe($p) {
        $subscribtion = user_subscription::get($p["subscriptionID"]);
        $user  = user::active();
    
        if(!user::active()->checkAccess("user:unsubscribe",array(
            "subscribtion" => $subscribtion
        ))) {
            mod::msg("Не удалось удалить подписку",1);
            return false;
        }
        $subscribtion->delete();
        return true; 
    }

}
