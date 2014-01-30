<?

/**
 * Модель заказа (он же модель корзины)
 **/
class eshop_order extends reflex implements mod_handler {

    /**
     * Видимость для браузеров
     **/
    public static function indexTest() {
        return true;
    }

    /**
     * Контроллер корзины (перенаправляет на страницу заказа)
     **/
    public static function index() {
        $url = eshop_order::cart()->url();
        header("location:$url");
        die();
    }

    /**
     * Контроллер страницы заказа
     **/
    public static function index_item($p) {
        $order = self::get($p["id"]);
        if(!$order->my())
            mod_cmd::error(404);
        tmp::exec("eshop:order",$order,$p);
    }

    /**
     * Обновляет итоговую сумму заказа
     **/
    public final function updateTotal() {
        $this->data("total",$this->total());
    }

    public final function reflex_beforeCreate() {
        $this->data("created",util::now());
        $this->data("security",util::id());
    }

    public final function reflex_beforeOperation() {
        $this->data("changed",util::now());
    }

    public function on_eshop_cartContentChanged($p) {

        $cart = $p->param("cart");
        $cart->updateTotal();
        $cart->data("changed",util::now());

        mod::fire("eshop_cartChanged",array(
            "order" => $this,
            "cart" => $this,
            "deliverToClient" => true,
        ));

    }

    /**
     * Возвращает true/fasle, в зависимости от того, принадлежит ли данный заказ активному пользователю
     **/
    public function my() {
        $user = user::active();
        if($user->exists() && $user->id() == $this->data("userID")) return true;
        $s = util::splitAndTrim($_COOKIE[self::$cookieMyOrders],",");
        if(in_array($this->data("security"),$s)) return true;
        return false;
    }

    /**
     * Возвращает true/fasle, в зависимости от того, может ли пользователь редактировать данный заказ
     * Редактировать можно новый заказ (тот которому еще не присвоен статус) + заказ должен
     * быть "своим", т.е. создан текущим пользователем.
    **/
    public function editable() {
        if(!$this->my())
            return false;
        if($this->data("status"))
            return false;
        return true;
    }

    /**
     * Возвращает все заказы пользователя
     **/
    public function myOrders() {
        $user = user::active();
        if($user->exists()) {
            $ret = self::all()->eq("userID",$user->id());
        } else {
            $s = util::splitAndTrim($_COOKIE[self::$cookieMyOrders],",");
            $ret = self::all()->eq("security",$s);
        }
        return $ret;
    }

    /**
     * Возвращает пользователя, сделавшего заказ
     **/
    public function user() {
        $user = user::get($this->data("userID"));
        if(!$user->exists()) {
            $user = reflex::virtual("user",array(
                "email" => $this->data("email"),
            ));
        }
        return $user;
    }

    public static function on_mod_beforeAction() {
    
        $user = user::active();
        
        if(!$user->exists()) {
            return;
        }
            
        $s = util::splitAndTrim($_COOKIE[self::$cookieMyOrders],",");
        
        foreach($s as $id) {
            reflex::get(get_class())->eq("security",$id)->eq("userID",0)->one()->data("userID",$user->id());
		}
		
        setcookie(self::$cookieMyOrders,false,-1,"/");
    }

    private static $cookie = "orderID";
    private static $cookieMyOrders = "fubqw5rd";

    /**
     * Изменяет статус заказа
     **/
    public final function setStatus($status) {

        $statusObj = eshop_order_status::get($status);

        if(!$statusObj->exists())
            return false;

        if($statusObj->beforeSet($this)===false)
            return;

        if($this->draft()) {
            $this->fixItems();
            $this->data("sent",util::now());
        }

        $this->data("status",$status);
        $this->log("Статус изменен на «{$statusObj->title()}»");

        $statusObj->afterSet($this);

        mod::fire("eshop_orderStatusChanged",array(
            "order" => $this,
            "status" => $status,
            "deliverToClient" => true,
        ));
    }


    /**
     * Вернет активный заказ (Корзину)
     **/
    public static function cart() {
        $id = $_COOKIE[self::$cookie];
        $order = reflex::virtual(get_class());
        if($id)
            $order = eshop_order::drafts()->eq("id",$id)->one();
        if(!$order->my())
            $order = reflex::virtual(get_class());
        return $order;
    }

    /**
     * Создает новый заказ
     * Прикрепляет его текущему пользователю
     **/
    public static final function createOrderForActiveUser() {
        $order = self::create(get_class(),array(
            "userID" => user::active()->id(),
        ));

        $id = $order->id();
        setcookie(self::$cookie,$id,time()+60*60*24*30,"/");
        $_COOKIE[self::$cookie] = $id;

        $order->addToCookies();

        return $order;
    }

    /**
     * Записываем секретный ключ заказа в список 'мойх заказов'
     **/
    public function addToCookies() {

        $order = $this;
        $s = util::splitAndTrim($_COOKIE[self::$cookieMyOrders],",");
        $s[] = $order->data("security");
        $s = array_unique($s);
        $s = implode(",",$s);
        setcookie(self::$cookieMyOrders,$s,time()+60*60*24*30,"/");
        $_COOKIE[self::$cookieMyOrders] = $s;
    }

    /**
     * Создает новый пустой заказ на основе данного
     * Копируются все поля заказа, но не товары в нем
     **/
    public function duplicateEmpty() {

        $order = self::create(get_class($this),$this->data());

        if($this->my())
            $order->addToCookies();

        return $order;
    }

    /**
     * Возвращает коллекцию товаров данного заказа
     **/
    public function items() {
        $ret = eshop_order_item::all()->eq("orderID",$this->id())->limit(0);
        if(!$this->exists()) $ret->eq("id",-1);
        return $ret;
    }

    /**
     * Добавляет $n товаров с id = $itemID в данный зазаз
     * @return class eshop_order_item добавленный элемент
     **/
    public function addItem($itemID,$n = 1, $itemSku = null) {

        if($n<=0)
            reflex::virtual("eshop_order_item");
        
        $items = $this->items();
        $items->eq("itemID", $itemID);
        if ($itemSku) {
            $items->eq("itemSku", $itemSku);
        }
        
       
        $item = $items->one();
        
        
        if(!$item->exists())
            $item = reflex::create("eshop_order_item",array(
                "orderID" => $this->id(),
                "itemID" => $itemID,
                "itemSku" => $itemSku,
            ));
            
        $item->setQuantity($item->quantity()+$n);

        mod::fire("eshop_addToCart",array(
            "cart" => $this,
            "itemID" => $itemID,
            "item" => $item,
            "itemSku" => $itemSku,
            "quantity" => $n,
            "deliverToClient" => true,
        ));
        
        return $item;
        
    }

    /**
     * Уменьшить количество товаров на $n
     * @param $integer $itemID ID товарной позиции
     * @param $integer $n На сколько надо уменьшить количество
     * Если количество товаров в позиции получится <=0, эта позиция удаляется из заказа
     **/
    public function decreaseItem($itemID,$n=1) {

        if($n<=0)
            return;

        $item = $this->items()->eq("itemID",$itemID)->one();
        if(!$item->exists())
            return;

        $quantity = $item->quantity()-$n;
        if($quantity<=0)
            $item->delete();
        else
            $item->setQuantity($quantity);
    }

    /**
     * Добавляет в заказ $n товаров $itemID
     * Если товар в таким itemID существует, меняет его количество
     * Если $n=0, удаляет позицию
     **/
    public function setQuantity($itemID,$n=1) {

        $item = $this->items()->eq("itemID",$itemID)->one();
        if($n>0) {
            if(!$item->exists())
                $item = reflex::create("eshop_order_item",array(
                    "orderID" => $this->id(),
                    "itemID" => $itemID
                ));
                $item->setQuantity($n);
        } else {
            $item = $this->items()->eq("itemID",$itemID)->one()->delete();
        }

    }

    /**
     * Возвращает полную стоимость заказа
     **/
    public function total() {
        $total = 0;
        foreach($this->items() as $item)
            $total += $item->data("quantity")*$item->price();
        return $total;
    }

    /**
     * @return Возвращает суммарное количество товаров в заказе
    **/
    public function totalNumber() {
        $total = 0;
        foreach($this->items() as $item)
            $total += $item->data("quantity");
        return $total;
    }

    /**
     * Возвращает сообщение для пользователя о статусе заказа (поле `message` класса eshop_order_status)
    **/
    public function message() {
        $ret = trim($this->status()->descr());
        if(!$ret)
            $ret = $this->status()->title();
        return $ret;
    }

    /**
     * Очищает данный заказ
     * @return $this
     **/
    public function clear() {
        $this->items()->delete();
    }

    /**
     * Возвращает объект статуса заказа
     **/
    public function status() {
        return eshop_order_status::get($this->data("status"));
    }

    /**
     * @return true/false в зависимости от того черновик это или нет
     **/
    public function draft() {
        return !$this->data("status");
    }

    /**
     * Фиксирует цену и название у товаров в заказе
     * Метод вызывается в момент отправки заказа
     **/
    public function fixItems() {
        foreach($this->items() as $item)
            $item->fixItem();
    }

    /**
     * Возвращает коллекцию всех заказов
     **/
    public static function all() {
        return reflex::get(get_class())->desc("sent")->neq("status","");
    }

    /**
     * Возвращает коллекцию всех черновиков (неоформленных заказов)
     **/
    public static function drafts() {
        return reflex::get(get_class())->desc("created")->eq("status","");
    }

    /**
     * Возвращает заказ по ID
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    /**
     * Возвращает дату заказа
     * Для отправленых заказов дата заказа - дата отправки
     * Для неотправленных заказов дата заказа - дата создания
     **/
    public function date() {
        return $this->draft() ? $this->pdata("changed") : $this->pdata("sent");
    }

    /**
     * @return Массив дочерних элементов для каталога
     **/
    public function reflex_children() {
        return array(
            $this->items()->title("Состав заказа"),
        );
    }

    /**
     * Отделяет от заказа те товары, которые есть в наличии
     * Создает новый заказ с этими товарами
     * Товары, которых нет в наличии остаются в текущем заказе
     **/
    public function detachInstockItems() {
        $order = $this->duplicateEmpty();
        foreach($this->items() as $item) {
            
            // Расчитываем сколько элементов в наличии мы можем перебросить в новый заказ
            $n = min($item->item()->inStock(),$item->quantity());
            
            // Добавляем в новый заказ элементы которые в наличии из текущего заказа
            if($n>0) {
                $itemID = $item->item()->id();
                $item2 = $order->addItem($itemID,$n);
                $item2->data("price",$item->data("price"));
                $item2->data("title",$item->data("title"));
                $this->decreaseItem($itemID,$n);
            }
        }
        
        return $order;
    }

    /**
     * @return true/false в зависимости от того, в наличии ли ВСЕ товары в заказе в достаточном количестве
     **/
    public function allInStock() {
        foreach($this->items() as $item){
            if($item->item()->data("instock") < $item->data("quantity"))
                return false;
        }
        return true;
    }
    
    /**
     * @return bool
     * Возвращает true если в заказе есть как товары в наличии так и не в наличии
     **/
    public function partiallyInStock() {

        // Если в наличии 0 товаров, возвращаем false,
        // т.к. нам нужен именно «смешанный» заказ
        if($this->instockNumber()==0)
            return false;

        // Если какого-то товара в наличии меньше чем в заказе, возвращаем true
        foreach($this->items() as $item) {
            if($item->item()->data("instock") < $item->data("quantity"))
                return true;
        }

        // Если мы дошли до этого момента, все товары в наличии. Возвращаем false
        return false;
    }

    /**
     * Количество товаров из данного заказа, которе есть в наличии
     **/
    public function instockNumber() {
        $n = 0;
        foreach($this->items() as $item){
            $n+= min($item->item()->inStock(),$item->quantity());
        }
        return $n;
    }

}
