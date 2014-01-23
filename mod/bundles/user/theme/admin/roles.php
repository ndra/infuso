<? 

admin::header("Роли и операции");
tmp::reset();

<div style='padding:20px;' >

    foreach(user_role::all()->eq("parents","") as $role) {
        tmp::exec("branch",array(
            "operation" => $role
        ));
    }

</div>

admin::footer();