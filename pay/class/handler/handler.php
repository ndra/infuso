<?

class pay_handler implements mod_handler {

    public function on_mod_init() {
     
        user_operation::create("pay:viewReportInvoices","Просмотр отчета модуля Pay")
            ->appendTo("admin");
            
    }
}