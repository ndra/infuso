<?

/**
 * Публичный контроллер для подгрузки списка рагионов и городов
 **/
class geo_controller extends mod_controller {

	public function postTest() {
		return true;
	}

	public function post_regions($p) {

		$country = geo_country::get($p["countryID"]);
		$ret = array();
		foreach($country->regions()->limit(0) as $region)
		    $ret[$region->id()] = $region->title();
		return $ret;

	}

	public function post_cities($p) {

		$region = geo_region::get($p["regionID"]);
		$ret = array();
		foreach($region->cities()->limit(0) as $city)
		    $ret[$city->id()] = $city->title();
		return $ret;

	}

}
