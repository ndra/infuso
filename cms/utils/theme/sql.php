<? 

admin::header();

<form class='joe9zokei' method='post' >

    <textarea name='query' >
        echo util::str($_POST["query"])->esc();
    </textarea>
    
    <input type='submit' value='Выполнить' />
    
    if($_POST["query"]) {
        try {
        
            $result = mod::service("db")->query($_POST["query"])->exec();
            
            <table class='result' >
                
                foreach($result->fetchAll() as $rowIndex => $row) {
                
                    // Заголовок таблицы
                    if($rowIndex == 0) {
                        <thead>
                            <tr>
                                foreach($row as $key => $val) {
                                    <td>{$key}</td>
                                }
                            </tr>
                        </thead>
                    }
                
                    <tr>                
                        foreach($row as $key => $val) {
                            <td>
                                echo \infuso\util\util::str($val)->esc();
                            </td>        
                        }
                    </tr>
                }
                
            </table>
            
        } catch(Exception $ex) {
            mod::msg($ex->getMessage(),1);
        }
    }
    
</form>

admin::footer();