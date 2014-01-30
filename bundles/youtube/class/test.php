<?

class youtube_test extends mod_controller {

	public static function indexTest() {
		return true;
	}
	
	public static function index() {
		$feed = youtube_feed::get()->q("iphone 3g");
		echo $feed->first()->player();
	}

}
