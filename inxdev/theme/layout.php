<?

tmp::reset();

tmp::header();
tmp::exec("header");

echo "<div style='width:900px;margin:20px auto 20px auto;' >";

tmp::region("center");

echo "</div>";


tmp::exec("footer");
tmp::footer();