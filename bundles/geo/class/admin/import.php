<?

/**
 * Контроллер-загрузчик объектов в базу
 **/ 
class geo_admin_import {

	public function import() {

		geo_city::all()->delete();
		set_time_limit(0);
		$xml = simplexml_load_file(file::get("/geo/data/classifier.xml")->native());
		foreach($xml->city as $city) {
			reflex::create("geo_city",array(
				"id" => $city->city_id."",
				"title" => $city->name."",
				"regionID" => $city->region_id."",
			),true);
			reflex::freeAll();
		}

		foreach($xml->region as $region) {
			reflex::create("geo_region",array(
				"id" => $region->region_id."",
				"title" => $region->name."",
				"countryID" => $region->country_id."",
			),true);
			reflex::freeAll();
		}

		foreach($xml->country as $country) {
			reflex::create("geo_country",array(
				"id" => $country->country_id."",
				"title" => $country->name."",
			),true);
			reflex::freeAll();
		}

	}

}
