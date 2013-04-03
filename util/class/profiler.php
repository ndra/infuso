<?

class util_profiler {

    public function profiler() {
    
        $fn = tmp_delayed::add(array(
            "class" => "util_profiler",
            "method" => "showProfiler",
            "priority" => 100000
        ));
        echo $fn;
        
        tmp::get("util:profiler")->includeScriptsAndStyles();
        tmp::jq();

    }

    public static function showProfiler() {
    
        if(!mod_superadmin::check())
            return;
            
        tmp::exec("/util/profiler");
        
    }

}
