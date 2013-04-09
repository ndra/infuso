<? 

tmp::header();

<h1>{$topic->title()}</h1>

foreach($topic->posts() as $post) {
    <div>
        $post->message();
    </div>
}

tmp::exec("createPost");

tmp::footer();