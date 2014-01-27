<?

/**
 * Контроллер отчета выгрузки в Яндекс.Маркет
 **/ 
class eshop_yandexMarket_report extends mod_controller {

    public static function indexTest() {
        return user::active()->checkAccess("eshop:yandexMarket:showReport");
    }

    public function index() {
        tmp::exec("/eshop/admin/yandex-market/report");
    }    

}
