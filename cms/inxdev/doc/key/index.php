<?

inx::add();

?>
<script>
    $(function() {
        setTimeout(function() {
            inx.service("key").on("keypress",function(e){
                inx.msg(e.char);
            });
        },1000);
    });
</script>