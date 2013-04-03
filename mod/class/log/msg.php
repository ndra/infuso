<?

class mod_log_msg {

	public function __construct($data) {
		$this->data = $data;
	}

	public function text() {
		return $this->data["text"];
	}
	
	public function error() {
		return !!$this->data["error"];
	}
	
	public function count() {
		return $this->data["count"];
	}

}
