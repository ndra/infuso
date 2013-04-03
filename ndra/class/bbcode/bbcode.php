<?

/**
 * Служба парсера bbcode
 **/
class ndra_bbcode extends mod_service {

	public function defaultService() {
	    return "bbcode";
	}

    public function initialParams() {
    
        return array(
            "bb" => array(
		        array (
					"start" => 'b',
					"content" => '(.+)',
					"end" => '/b',
					"flags" => 'Usi',
					"replace" => '<b>$1</b>',
				), array (
					"start" => 'u',
					"content" => '(.+)',
					"end" => '/u',
					"flags" => 'Usi',
					"replace" => '<u>$1</u>',
				), array (
					"start" => 'i',
					"content" => '(.+)',
					"end" => '/i',
					"flags" => 'Usi',
					"replace" => '<i>$1</i>',
				), array (
					"start" => 'url=([^]]+)',
					"content" => '(.+?)',
					"end" => '/url',
					"flags" => 'i',
					"replace" => '<a href="$1" >$2</a>',
				), array (
					"start" => 'img',
					"content" => '([^]]+)',
					"end" => '/img',
					"flags" => 'Ui',
					"replace" => '<img src="$1">',
				), array (
					"start" => 'quote',
					"content" => '(.+)',
					"end" => '/quote',
					"flags" => 'Usi',
					"replace" => '<blockquote class="na"><div>$1</div></blockquote>',
				), array (
					"start" => 'quote="([^"]+)"',
					"content" => '(.+)',
					"end" => '/quote',
					"flags" => 'Usi',
					"replace" => '<blockquote><cite><b>$1 писал(а):</b></cite><div>$2</div></blockquote>',
				),
		    ),
		);
    }

    public function parse($text) {
    
        $text = strip_tags($text);
        
        foreach ($this->param("bb") as $tag) {
            $text = preg_replace('#\['.$tag["start"].'\]'.$tag["content"].'\['.$tag["end"].'\]#'.$tag["flags"],$tag["replace"],$text);
        }

        // чистка неверных тегов (те, что остались после замен)
        $text = preg_replace('#(\[.+\]|\[/.+\])#Ui','',$text);
        
        $text = nl2br($text);
        $text = str_replace('</ul><br />','</ul>',$text);
        $text = str_replace('</div><br />','</div>',$text);
        
        return $text;
    }
    
    /**
     * Очищает текст от bb-кода
     **/
    public function stripBBCodes($text) {
        return util::str($this->parse($text))->text();
    }

}
