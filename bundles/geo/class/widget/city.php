<?

/**
 * Виджет выбора страны-региона-города
 **/
class geo_widget_city extends \mod\template\widget {

	public function __construct() {
		$this->param("countries",geo_country::all());
	}

	public function name() {
		return "Выбор города";
	}

	public function execWidget() {

		$city = $this->param("city");
	    if(!$city) {
		    $ip = geo_ip::current();
		    $city = $ip->city();
		}

		if(is_string($city)) {
		    $city = geo_city::byName($this->param("city"));
		}

		$this->param("city",$city);

		tmp::exec("geo:citySelector",$this->param());
	}

	public function countries($countries="") {
		$countries = util::str($countries)->lower();
		$countries = util::splitAndTrim($countries,",");
		$countries = geo_country::all()->eq("lower(title)",$countries);
		$this->param("countries",$countries);
		return $this;
	}

}
