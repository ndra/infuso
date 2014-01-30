<?

tmp::reset();
tmp::header();

echo "<div style='width:400px;margin:200px auto;' >";
echo "<h2 style='padding:0 0 20px 0;' >Менеджер заявок. Веб-студия ndra.</h2>";
tmp::exec("user:loginForm");
echo "</div>";

tmp::footer();

?>
