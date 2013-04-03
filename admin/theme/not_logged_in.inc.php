<? tmp::header();

tmp::css("/admin/res/login.css");
inx::add(array(
    "type" => "inx.mod.admin.login",
    "startup" => "true",
));

tmp::footer(); ?>
