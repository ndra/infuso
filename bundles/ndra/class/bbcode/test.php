<?

/**
 * Набор тестов для bbcode
 **/

class ndra_bbcode_test extends mod_controller {

	public function indexTest() {
	    return true;
	}
	
	public function index() {
	    tmp::header();
	    $example =
			"This is [b]bold[/b].\n".
			"This is [i]italic[/i]\n".
			"This is [u]underline[/u]\n".
			"This is [url=http://www.google.com]underline[/url]\n".
			"This is image [img]http://yandex.st/morda-logo/i/logo.svg[/img]\n".
			"This is [quote]quote[/quote]";
	    echo "source:<br/>".nl2br($example)."<br/><br/>";
	    echo "parsed:<br/>".mod::service("bbcode")->parse($example);
	    tmp::footer();
	}

}
