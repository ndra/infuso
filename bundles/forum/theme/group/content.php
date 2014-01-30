<? 

<h1>{$group->title()}</h1>

<div class='c122dt8brg' >
    <table>
    
        <tr>
            <td>Тема</td>
            <td>Просмотров</td>
            <td>Постов</td>
        </tr>
    
        foreach($group->topics() as $topic) {    
            <tr>
                <td>
                    <a href='{$topic->url()}' >{$topic->title()}</a>
                </td>            
                <td>
                    echo $topic->countViews();
                </td>
                <td>
                    echo $topic->countPosts();
                </td>
            </tr>
        }
    </table>
</div>