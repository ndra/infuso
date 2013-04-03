<?

$vote = $p1;
if($vote->exists()) {    

    echo "<form>";
    echo "<h2>{$vote->data('title')}</h2>";
    
    switch($vote->data("mode")) {
        case 1:
            foreach($vote->options() as $option) {
                echo "<div class='oih93un-optionContainer'>";
                $id = util::id();
                echo "<input type='radio' id='$id' name='option' value='{$option->id()}' >";
                echo "<label for='$id' >";
                echo $option->title();
                echo "</label>";
                echo "</div>";
            }
            break;
        case 2:
            foreach($vote->options() as $option) {
                echo "<div class='oih93un-optionContainer'>";
                $id = util::id();
                echo "<input type='checkbox' id='$id' name='{$option->id()}' value='1' >";
                echo "<label for='$id' >";
                echo $option->title();
                echo "</label>";
                echo "</div>";
            }
            break;
        case 3:
            echo "<input type='text' />";
            break;
    }
    
    // Скрытое поле с id голосования
    echo "<input type='hidden' name='voteID' value='{$vote->id()}' />";    
        
    // Обязательно оставьте класс urxp1-submit, иначе скрипт отправки не сработает
    echo "<input class='urxp1-submit' type='button' value='Ответить' >";
    echo "</form>";
    
} else {
    echo "Нет активных голосований";
}