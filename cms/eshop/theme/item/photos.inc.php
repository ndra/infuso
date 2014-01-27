<?

ndra_fancybox::add(".fancybox");

foreach($p1->photos() as $photo) {
    $preview = $photo->preview(100,100)->crop();
    echo "<a rel='fancybox' class='fancybox' href='{$photo->path()}' ><img src='{$preview}' /></a>";
}

?>