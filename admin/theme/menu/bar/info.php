<?

<div class='nqalth29om' >

    $info = user::active()->data();
    echo "<img src='/admin/res/user.gif' align='absmiddle' style='margin-right:5px;' />";
    echo $info ? $info["email"] : "Вход не выполнен";
    echo " ";
    if(\infuso\core\superadmin::check()) {
        echo ", root";
    }
    inx::add();

</div>

?>

<script>
$(".nqalth29om").click(function(){
    inx({type:"inx.mod.admin.login"}).cmd("render");
});
</script>
