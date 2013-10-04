<? 

tmp::header();

tmp::exec("controls");

$mode = mod::app()->url()->query("mode");

switch($mode) {
    case "month":
        tmp::exec("month");
        break;
    default:
    case "year":
        tmp::exec("year");
        break;
}

tmp::footer();