<?

class youtube_video {

    public static function get($id) {
        return new self($id);
    }

    private function __construct($id) {

        if(preg_match("/http\:\/\//",$id)) {
            $url = mod_url::get($id);
            $videoID = $url->query("v");
        }
        
        if(preg_match("/http\:\/\/youtu\.be/",$id)){
            $videoID = str_replace("http://youtu.be/","",$id);
        }
        
        if(preg_match("/http\:\/\/www\.youtube\.com/",$id)){
            $videoID = str_replace("http://www.youtube.com/v/","",$id);
        }
        
        if(!$videoID){
            $videoID = $id;    
        }
        
        $this->id = $videoID;
    }

    public function id() {
        return $this->id;
    }

    public function exists() {
        return !!$this->id();
    }
    
    public function preview() {

        $querry=@file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$this->id()}?alt=json");
        $mas=json_decode($querry,true);
        return $mas["entry"]['media$group']['media$thumbnail']['0']['url'];
    }
    
    public function player($width=560,$height=315) {
        if(!$this->exists())
            return "";
        return "<iframe width='$width' height='$height' src='http://www.youtube.com/embed/{$this->id()}' frameborder='0' allowfullscreen></iframe>";
    }

}
