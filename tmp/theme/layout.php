<?

tmp::header();
tmp::reset();
tmp::exec("header");
tmp_lib::components();

<table class='pwq3nk3agh' >
    <tr>
    
        // Левая часть
        <td class='left' >
            <div>
                tmp::region("left");
            </div>
        </td>
        
        // Центральная часть
        <td class='center' >
            tmp::region("center");
        </td>
        
        // Правая часть
        <td class='right' >
            <div>
                tmp::region("right");
            </div>
        </td>
    
    </tr>
</table>

tmp::obj()->editor()->placeWidget();

tmp::exec("footer");

tmp::footer();