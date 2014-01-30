<?

class pay_handler implements mod_handler {

    public function on_mod_init() {
     
        user_operation::create("pay:viewReportInvoices","Просмотр отчета модуля Pay")
            ->appendTo("admin");
            
    }
    
    /**
     * Обработчик собития Cron-а, проверка статуса счетов, помеченых на такую проверку
     *
     * @return void
     **/
    public function on_mod_cron() {

        $invoiceList = pay_invoice::all()
            ->eq("status", pay_invoice::STATUS_CHECK)
            ->geq("timeCheck", pay_invoice::getCheckRefreshTime())
            ->asc("timeCheck")
            ->limit(pay_invoice::getCheckRefreshLimit());

        foreach ($invoiceList as $invoice) {

            if ($driver = $invoice->data("driverUseonly")) {

                //Вызвать проверку статуса счета у драйвера
                $invoice->driver($driver)->check();

            } else {
                //Не возможно проверить данный статус, скорее всего это ошибка программиста

                //Отменяем данный счет
                $invoice->status(pay_invoice::STATUS_CANCELED);

                //Пишем в лог
                $invoice->log("Error: не указан драйвер для проверки статуса счета");
            }

        } //END foreach
    }
    
}
