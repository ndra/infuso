<?

echo "<div class='iizjmykhy6-container' >";

foreach(admin_widget::all() as $widget) {
    if($widget->inStartPage()) {
        echo "<div class='iizjmykhy6-widget' style='width:{$widget->width()}px;' >";
        $widget->exec();
        echo "</div>";
    }
}

echo "</div>";