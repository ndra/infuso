<? 

admin::header("Счета (отчет)");

tmp::exec("invoices");
tmp::exec("acount");
tmp::exec("lastAccountOperations");

admin::footer();