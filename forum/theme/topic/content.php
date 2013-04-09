<?

<h1>{$topic->title()}</h1>

<div class='ufqcsih0s7' >
    <table class='posts' >
        foreach($posts as $post) {
        
            <tr>
                <td>
                    echo $post->user()->title();
                </td>
                <td>
                    <div>
                        <div>
                            <a id='post-{$post->id()}' ></a>
                            echo $post->pdata("date")->text();
                        </div>
                        echo $post->message();
                        tmp::exec("files",array(
                            "post" => $post,
                        ));
                    </div>
                </td>
            </tr>
            
        }
    </table>
    
    tmp::exec("/reflex/navigation/pager",$posts);
    
</div>