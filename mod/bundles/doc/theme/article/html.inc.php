<?

$pn = 1;
foreach($p1->xml()->children() as $child) {
    switch($child->getName()) {
        case "p":
            echo "<div style='margin:0px 0px 10px 0px;'>";
            $title = $child->attributes()->title;
            if($title) {
                echo "<a name='$pn'></a>";
                echo "<h2>$title</h2>";
                $pn++;
            }
            $html = $child->asXML();
            echo preg_replace(array("/<p[^<]*>/","/<\/p>/"),array("",""),$html);
            echo "</div>";
            break;
        case "code":
            echo "<pre style='margin:0px 0px 10px 0px;background:#ededed;padding:10px;border:1px dashed gray;' >";
            //$html = $child->asXML();
            //$html = preg_replace(array("/^<[^<]*>/","/<[^<]*>$/"),array("",""),$html);
            //$html = preg_replace(array("/^\<!\[CDATA\[/","/\]\]>$/"),array(""),$html);
            //$html = htmlspecialchars($html);
            $html = $child;
            echo htmlspecialchars($html);
            echo "</pre>";
            break;
            
        case "img":
            echo "<div style='margin:0px 0px 10px 0px;' >";
            echo "<div style='width:320px;background:#ededed;padding:10px;border:1px solid #cccccc;' >";
            $file = $p1->resourcePath()."/".$child."";
            $preview  = file::get($file)->preview()->resize()->width(320)->height(230);
            echo "<a href='$file'><img src='$preview' /></a>";
            echo $child->attributes()->title;
            echo "</div>";
            echo "</div>";
            break;
    }
}

?>