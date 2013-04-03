<? 

tmp::exec("comment:comment",$this->param());
$comments = comment::all()->eq("for",$for);

<div class='w992ykaasp' >    

    if($comments->count()) {
        foreach($comments as $comment) {
            <div class='item' >
            
                <div class='head' >
                    <div class='date' >{$comment->pdata(datetime)->txt()}</div>   
                    <br/>         
                    <span class='mark' >Оценка: </span>            
                    <span class='stars' >
                        for($i=1;$i<=5;$i++) {
                            if($i<=$comment->data(mark))
                                echo "&#9733;";
                            else
                                echo "&#9734;";
                        }
                    </span>
                </div>
                
                if($txt = $comment->data("text")) {
                    <div>
                        <b>Комментарий: </b>
                        echo $txt;
                    </div>
                }
                
                if($txt = $comment->data("plus")) {
                    <div>
                        <b>Достоиства: </b>
                        echo $txt;
                    </div>
                }
                
                if($txt = $comment->data("minus")) {
                    <div>
                        <b>Недостатки: </b>
                        echo $txt;
                    </div>
                }
                
            </div>
        }
    } else {
        echo "Отзывов пока нет, вы можете стать первым!";
    }

</div>