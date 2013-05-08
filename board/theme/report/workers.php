<? 

tmp::header();

foreach(user::all()->like("roles","boardUser") as $user) {
    tmp::exec("user",array(
        "user" => $user,
    ));
}

tmp::footer();