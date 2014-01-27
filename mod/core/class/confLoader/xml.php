<?

/**
 * Класс для загрузки xml конфигурации
 **/
class mod_confLoader_xml {

	/**
	 * xml => array()
	 * Читает строку, содержащую xml или объект xml в массив php
	 **/
	public static function read($doc) {

		if(is_string($doc)) {
			$doc = @simplexml_load_string(mod_file::get($doc)->data());
		}
			
		if(!$doc)
		    return false;

		$ret = array();
		
		foreach($doc->children() as $child) {
			if($child->getName()=="param") {
				$ret[$child->attributes()->name.""] = trim($child."");
			} elseif($child->getName()=="set") {
				$ret[$child->attributes()->name.""] = self::read($child);
			}
		}
				
		return $ret;
	}

	/**
	 * array() => xml
	 * Преобразует массив php в строку с xml
	 **/
	public static function write($conf,$doc=null) {

		if(func_num_args()==1) {
		    $doc = simplexml_load_string("<set></set>");
		}

		foreach($conf as $name=>$item) {
		
			if(is_array($item))	{
			    $set = $doc->addChild("set");
			    $set->addAttribute("name",$name);
			    self::write($set,$item);
			    
			} else {
			    $param = $doc->addChild("param",htmlspecialchars($item));
			    $param->addAttribute("name",$name);
			}
		}

		if(func_num_args()==1) {
		    return self::prettyPrintXML(dom_import_simplexml($doc));
		}
	}
	
	private static function prettyPrintXML($xml,$root=1) {

		if(get_class($xml)=="SimpleXMLElement")
			$xml = dom_import_simplexml($xml);


	    $ret = array();
		switch($xml->nodeType) {
		    case 9:
				$ret = self::prettyPrintXML($xml->firstChild,0);
				break;
			case 1:
			    $start = '<'.$xml->nodeName;
			    $attr = array();
				foreach($xml->attributes as $attribute)
			        $attr[] = $attribute->nodeName."="."'".htmlspecialchars($attribute->nodeValue,ENT_QUOTES)."'";
		        $start.= sizeof($attr) ? " ".implode(" ",$attr)." " : "";
				$start.=">";

			    if($xml->childNodes->length==1 & $xml->firstChild->nodeType==3) {
			    	$ret[] = $start.htmlspecialchars($xml->firstChild->nodeValue,ENT_QUOTES).'</'.$xml->nodeName.'>';
			    }
			    else {
			        $ret[] = $start;
				    foreach($xml->childNodes as $child)
				        foreach(self::prettyPrintXML($child,0) as $str)
				            $ret[] = "\t".$str;
				    $ret[] = '</'.$xml->nodeName.'>';
			    }
			    break;
			case 3:
			    if(trim($xml->nodeValue))
			    	$ret[] = htmlspecialchars(trim($xml->nodeValue),ENT_QUOTES);
			    break;
		}

		if(!$root) return $ret;
		else return implode("\n",$ret);
	}

}
