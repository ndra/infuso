<? class inxdev_build extends mod_controller {

public static function mount() {

	mod::msg("Building inx download");

	for($i=0;$i<=2;$i++) {
		file::get("/inxdev/build/tmp")->delete(true);
		file::mkdir("/inxdev/build/tmp");
		inx_mount_file::conf("dest","/inxdev/build/tmp/inx/");
		inx_mount_file::conf("pack",$i);
		inx_mount::buildModule("inx");

		foreach(file::dir("/inxdev/build/common") as $file)
		    echo $file->copy("/inxdev/build/tmp/{$file->name()}");

		$name = $i ? "full" : "packed";
		file_file2::get("/inxdev/build/tmp")->zip("/inxdev/build/$name.zip");
	}
	
	file::get("/inxdev/build/tmp/")->delete(true);
}

} ?>
