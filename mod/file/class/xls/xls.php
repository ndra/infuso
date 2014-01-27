<?

class file_xls {

	public function __construct($path) {
	    $this->path = $path;
	}

	public function data() {
	    require_once(file::get("/file/class/xls/oleread.inc")->native());
	    require_once(file::get("/file/class/xls/reader.inc")->native());
	    $data = new Spreadsheet_Excel_Reader();
	    $data->setOutputEncoding('utf-8');
	    $data->read(file::get($this->path)->native());
	    $data = $data->sheets[0]["cells"];
	    return $data;
	}

} ?>
