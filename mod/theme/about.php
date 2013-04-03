<? 

admin::header("Лицензионное соглашение");
    <div style='padding:40px;width:500px;' >
    
        <div style='margin-bottom:50px;' >
            echo "Этот сайт работает на системе управления <b>infuso</b>. Разработчик — веб-студия <a href='http://ndra.ru' target='_blank' >ndra</a>";
        </div>
    
        echo file::get("/mod/license.html")->data();
    </div>
admin::footer();