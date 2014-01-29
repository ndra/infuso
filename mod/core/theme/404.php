<?

tmp::header();

<div style='padding:100px;' >
    <div style='font-size:100px;' >404</div>
    echo "Page <b>".mod::app()->url()."</b> not found on server.";
</div>

tmp::footer();