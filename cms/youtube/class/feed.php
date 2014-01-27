<?

/**
 * Класс фида на youtube.com
 **/
class youtube_feed implements Iterator{

	// Итераторская шняга
	protected $items = array();
	public function rewind() { $this->load(); reset($this->items); }
	public function current() { $this->load(); return current($this->items); }
	public function key() { $this->load(); return key($this->items); }
	public function next() { $this->load(); return next($this->items); }
	public function valid() { $this->load(); return $this->current() !== false; }

	public static function get() {
		return new self();
	}

	public final function param($key=null,$val=null) {
	    if(func_num_args()==0) {
	        return $this->params;
	    } elseif(func_num_args()==1) {
	        return $this->params[$key];
	    } else {
	        $this->params[$key] = $val;
	        return $this;
	    }
	}

	public function q($q) {
		$this->param("q",$q);
		return $this;
	}

	private $loaded = false;
	public function load() {

	    if($this->loaded)
	        return;

	    $params = $this->params;
	    $params["v"] = 2;
	    $params["alt"] = json;
	    $q = "http://gdata.youtube.com/feeds/api/videos?".http_build_query($params);

	    // Пытаемся достать данные из кэша
	    $key = "youtube:".$q;
	    $videos = mod_cache::get($key);
	    
	    if(!$videos) {
	    
	        $data = file_get_contents($q);
		    $data = json_decode($data,1);
		    $data = $data["feed"]["entry"];
		    $ret = array();
		    if($data)
		        foreach($data as $item) {
		            preg_match("/[a-z0-9\-\_]+$/i",$item["id"]["\$t"],$matches);
		            $id = $matches[0];
		            $ret[] = array(
		                "id" => $id,
					);
		        }
		        
	        mod_cache::set($key,serialize($ret));
	        $videos = $ret;
	        
	    } else {
	        $videos = unserialize($videos);
	    }
	    
		$this->items = array();
		foreach($videos as $item) {
		    $video = youtube_video::get($item["id"]);
		    $this->items[] = $video;
		}
		
	    $this->loaded = true;
	}

	public function first() {
		$this->load();
		$ret = $this->items[0];
		if(!$ret)
		    $ret = youtube_video::get(0);
		return $ret;
	}

}
