<?

class seotools_rewrite_controller extends mod_controller {

	public function postTest() {
		return true;
	}
	
	public function post_save($p) {
		
		$original = seotools_rewrite::normalizeText($p["original"]);
		$replacement = seotools_rewrite::normalizeText($p["replacement"]);
		reflex::create("seotools_rewrite",array(
		    "original" => $original,
		    "replacement" => $replacement,
		));
		
	}
	
}
