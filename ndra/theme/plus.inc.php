<?

/*css:
.yw4xnwsn7 {display:inline-block;vertical-align:top;}
*/

echo "<div class='yw4xnwsn7' >";
tmp::js("https://apis.google.com/js/plusone.js");
echo "<g:plusone></g:plusone>";
echo "</div>";


echo "<div class='yw4xnwsn7' style='margin-right:20px;' >";
tmp::js("http://connect.facebook.net/en_US/all.js#xfbml=1");
echo "<fb:like show_faces='false' width='240' ></fb:like>";
echo "</div>";

if($apiID=mod::conf("ndra:vkApiID")) {
    echo "<div class='yw4xnwsn7' >";
    tmp::js("http://userapi.com/js/api/openapi.js?18");
    tmp::head("<script type='text/javascript'> VK.init({apiId:$apiID,onlyWidgets: true}); </script>");
    echo "<span id='vk_like'></span>";
    echo "<script type='text/javascript'>VK.Widgets.Like('vk_like', {type: 'button'});</script>";
    echo "</div>";
}

?>