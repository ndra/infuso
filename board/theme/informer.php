<? 

tmp::header();
tmp::reset();

tmp::exec("my-graph");

<div style='padding:10px;' >
    tmp::exec("my-tasks");
</div>

<div style='padding:10px;' >    
    tmp::exec("log");
</div>

tmp::footer();