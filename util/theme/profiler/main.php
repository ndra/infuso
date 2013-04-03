<? 

<div>
    echo "generated: ".round(microtime(1)-$GLOBALS["infusoStarted"],2)." sec.";
</div>

<div>
    echo "classload: ".round($GLOBALS["infusoClassTimer"],4)." sec.";
</div>

<div>
    echo "Page size : ".util::bytesToSize1000(mod_profiler::getVariable("contentSize"));
</div>

<div>
    echo "Peak memory: ".util::bytesToSize1000(memory_get_peak_usage())." / ".ini_get("memory_limit");
</div>

echo mod_action::current()->canonical();

$obj = tmp::obj();
if($obj->exists()) {
    echo " ";
    <a href='{$obj->editor()->url()}' target='_blank' >Редактировать </a>
}