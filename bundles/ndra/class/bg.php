<? class ndra_bg extends mod_controller {

public static function indexTest() { return true; }
public static function index() {
    tmp::header();
    tmp::reset();
    tmp::head("<style>body{background:gray;padding:100px;}</style>");

//	for($i=0;$i<10;$i++)
	//	echo "<div style='width:200px;height:200px;display:inline-block;padding:20px;' class='yyy' >2342343243242</div>";
//	ndra_bg::theme(".yyy","white");

	for($i=0;$i<100;$i++)
		echo "<a href='/' >2342343243242</a> d dsf sdf sdfsdfdf";
    ndra_bg::theme("a","white-1",1);


	mod::coreJS();
    tmp::footer();
}

// -----------------------------------------------------------------------------

public static function add($selector,$position,$img,$hover=false) {
	tmp::jq();
	tmp::js("/ndra/res/bg/bg.js");
	$hover = json_encode($hover);
	tmp::script("$(function() {ndra.bg.add('$selector','$position','$img',$hover)});");
}

public static function theme($selector,$theme,$hover=null) {

	switch($theme) {
	    default:
			ndra_bg::add($selector,"-22,-22,15,15","url(/ndra/res/bg/white/tl.png)",$hover);
			ndra_bg::add($selector,"100%-15,-22,100%+22,15","url(/ndra/res/bg/white/tr.png)",$hover);
			ndra_bg::add($selector,"100%-15,100%-15,100%+22,100%+22","url(/ndra/res/bg/white/br.png)",$hover);
			ndra_bg::add($selector,"-22,100%-15,15,100%+22","url(/ndra/res/bg/white/bl.png)",$hover);
			ndra_bg::add($selector,"15,-22,100%-15,15","url(/ndra/res/bg/white/t.png)",$hover);
			ndra_bg::add($selector,"100%-15,15,100%+22,100%-15","url(/ndra/res/bg/white/r.png)",$hover);
			ndra_bg::add($selector,"15,100%-15,100%-15,100%+22","url(/ndra/res/bg/white/b.png)",$hover);
			ndra_bg::add($selector,"-22,15,15,100%-15","url(/ndra/res/bg/white/l.png)",$hover);
			ndra_bg::add($selector,"15,15,100%-15,100%-15","white",$hover);
			break;
		case "browser-frame":
			ndra_bg::add($selector,"-7,-20,149,0","url(/ndra/res/bg/browser-frame/tl.png)",$hover);
			ndra_bg::add($selector,"149,-20,100%-70,0","url(/ndra/res/bg/browser-frame/t.png)",$hover);
			ndra_bg::add($selector,"100%-70,-20,100%+7,0","url(/ndra/res/bg/browser-frame/tr.png)",$hover);
			ndra_bg::add($selector,"-7,100%,19,100%+12","url(/ndra/res/bg/browser-frame/bl.png)",$hover);
			ndra_bg::add($selector,"19,100%,100%-18,100%+12","url(/ndra/res/bg/browser-frame/b.png)",$hover);
			ndra_bg::add($selector,"100%-18,100%,100%+7,100%+12","url(/ndra/res/bg/browser-frame/br.png)",$hover);
			ndra_bg::add($selector,"-6,0,0,100%","url(/ndra/res/bg/browser-frame/l.png)",$hover);
			ndra_bg::add($selector,"100%,0,100%+6,100%","url(/ndra/res/bg/browser-frame/r.png)",$hover);
		    break;
        case "highlight":
        	ndra_bg::add($selector,"-9,-4,0,5","url(/ndra/res/bg/highlight/lt.png)",$hover);
        	ndra_bg::add($selector,"100%,-4,100%+9,5","url(/ndra/res/bg/highlight/rt.png)",$hover);
        	ndra_bg::add($selector,"100%,100%-5,100%+9,100%+4","url(/ndra/res/bg/highlight/rb.png)",$hover);
        	ndra_bg::add($selector,"-9,100%-5,0,100%+4","url(/ndra/res/bg/highlight/lb.png)",$hover);
			ndra_bg::add($selector,"-9,5,100%+9,100%-5","url(/ndra/res/bg/highlight/center.png)",$hover);
			ndra_bg::add($selector,"0,-4,100%,5","url(/ndra/res/bg/highlight/center.png)",$hover);
			ndra_bg::add($selector,"0,100%-5,100%,100%+4","url(/ndra/res/bg/highlight/center.png)",$hover);
		    break;
	}
}

} ?>
