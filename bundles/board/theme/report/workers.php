<? 

tmp::header();

tmp::reset();

<div style='padding:20px;' >
   
    foreach(user::all()->like("roles","boardUser") as $user) {
        tmp::exec("user",array(
            "user" => $user,
        ));
    }

</div>

tmp::footer();