<? 

if(user::active()->checkAccess("pay:viewReportInvoices")) {
    $url = mod::action("pay_admin/reportInvoices")->url();
    <a href='$url' >Отчет модуля Pay </a>
}