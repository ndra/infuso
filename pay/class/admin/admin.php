<?

/**
 * Контроллер для управления счетом пользователя из админки
 **/
class pay_admin extends mod_controller {

    public function indexTest() {
        return true;
    }

    public function postTest() {
        return true;
    }
    
    public function index_reportInvoices() {
    
        if(!user::active()->checkAccess("pay:viewReportInvoices")) {
            throw new Exception("Просмотр отчета недоступен");
        }
    
        tmp::exec("/pay/admin/reportInvoices");
    }
    
    public function post_addFunds($p) {
        $user = user::get($p["userID"]);
        $amount = $p["amount"] * 1;
        $user->addFunds($amount,"Пополнение счета администратором");
    }

}