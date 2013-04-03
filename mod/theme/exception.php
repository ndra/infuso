<?

if(mod_superadmin::check())
    tmp::exec("admin");
else
    tmp::exec("user");