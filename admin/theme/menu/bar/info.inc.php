<?

$info = user::active()->data();
echo "<img src='/admin/res/user.gif' align='absmiddle' style='margin-right:5px;' />";
echo $info ? $info["email"] : "Вход не выполнен";
echo " ";
if(mod_superadmin::check()) echo ", Админ включен";
inx::add();
?>

<script>
$("#raub2v07e-login-info").click(function(){
    inx({type:"inx.mod.admin.login"}).cmd("render");
});
</script>
