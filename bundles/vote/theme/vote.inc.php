<?

tmp::jq();
mod::coreJS();

foreach(vote::all() as $vote) {

    echo "<div class='urxp1' >";
    tmp::exec("ajax",$vote);
    echo "</div>";
    
}