<?

class youtube_video {

    public static function get($id) {
        return new self($id);
    }

    private function __construct($id) {

        if(preg_match("/http\:\/\//",$id)) {
            $url = mod_url::get($id);
            $id = $url->query("v");
        }

        $this->id = $id;
    }

    public function id() {
        return $this->id;
    }

    public function exists() {
        return !!$this->id();
    }

    public function player($width=560,$height=315) {
        if(!$this->exists())
            return "";
        return "<iframe width='$width' height='$height' src='http://www.youtube.com/embed/{$this->id()}' frameborder='0' allowfullscreen></iframe>";
    }

}
