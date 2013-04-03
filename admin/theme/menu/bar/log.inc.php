<?
inx::add();
echo "Лог";
?>

<script>
$("#raub2v07e-log-info").click(function(){
    inx({type:"inx.mod.admin.log"}).cmd("render");
});
</script>
