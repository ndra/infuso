<? class inxdev_processor {

public static function process($xml,&$meta=null) {
    $meta = array();
    foreach($xml->children() as $child) {
        switch($child->getName()) {
            case "title":
                $meta["title"] = $child."";
                break;
            case "id":
                $meta["id"] = $child."";
                break;
            case "p":
                echo "<div style='margin:0px 0px 10px 0px;'>";
                $title = $child->attributes()->title;
                if($title) echo "<h2>$title</h2>";
                $html = $child->asXML();
                echo preg_replace(array("/<p[^<]*>/","/<\/p>/"),array("",""),$html);
                echo "</div>";
                break;
            case "inx":
                echo "<div style='margin:0px 0px 10px 0px;' >";
                $json = json_decode(trim($child),1);
                ob_start();
                inx::add($json);
                $inxcode = ob_get_clean();
                echo $inxcode;
                echo "</div>";
                
                if($code = self::getCode($json["type"]))
                	self::collapsed($code,$json["type"]);
				self::collapsed($inxcode,"Код подключения");
				echo "<br/>";
                
                break;
            case "code":
                echo "<pre style='margin:0px 0px 10px 0px;background:#ededed;padding:10px;border:1px dashed gray;' >";
                echo $child;
                echo "</pre>";
                break;
			case "tmp":
			    echo "<div style='margin:0px 0px 20px 0px;' >";
			    tmp::exec($child."");
			    echo "</div>";
			    break;
                
        }
    }
}

public static function collapsed($html,$title) {
	$id = rand();
	echo "<div style='margin:0px 0px 5px 0px;' ><a href='#' onclick='$(\"#$id\").show(1000);return false;' >$title</a></div>";
	echo "<div id='$id' style='overflow:auto;display:none;margin:0px 0px 10px 0px;background:#ededed;padding:10px;border:1px dashed gray;' >";
	echo "<pre id='$id' style='' >";
	echo htmlspecialchars($html);
	echo "</pre>";
	echo "</div>";
}

public static function getCode($type) {
	$type = trim($type);
	$type = strtr($type,array("inx.mod.inxdev"=>"","."=>"/"));
	$file = "/inxdev/inx.mod.inxdev/".trim($type,"/").".js";
	return file::get($file)->data();
}

} ?>
