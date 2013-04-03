<? 

if(!$tag) {
    throw new Exception("Параметр \$tag не задан");
}

foreach($attributes as $key=>$val)
    $attributes[$key] = $key."='".util::str($val)->esc()."'";
    
$attributes = implode(" ",$attributes);

$selfClosing = array(
    "input",
    "img",
);

if(in_array($tag,$selfClosing)) {
    echo "<{$tag} {$attributes}>";
} else {
    echo "<{$tag} {$attributes}>{$content}</{$tag}>";
}