<? 

tmp::header();
tmp::reset();

<div class='ggbl9buyfa' >
    <h1>У нас проблемы!</h1>
    echo $exception->getMessage();
    
    <br/><br/>
    <b>Файл:</b> {$exception->getFile()}    
    <br/>
    <b>Строка:</b> {$exception->getLine()} 
    
    <br/><br/>
    <pre>
        echo $exception->getTraceAsString();
    </pre>
    
    if(rand()%100==0) {
        $feed = new youtube_feed();
        $feed->q("cat");
        echo $feed->first()->player();
    }
    
    //if(rand()%1==0) {
      //  <script>prompt('Вы хотите удалить все файлы модуля site?');</script>
   // }
    
</div>

tmp::footer();