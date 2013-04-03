<? 

/**
 * Параметры:
 * $city - город. Если не указан, город определяется по ip
 **/
 
tmp::jq();
mod::coreJS();
 
$region = $city->region();
$country = $region->country();

//проверяем тип данных которых нам надо вернуть
if(!in_array($returnData, array("id", "title"))){ 
    $returnData="id";    
}

//geo:returnData тип данных которые нам нужно что бы передавались на сервер для js
<div class='el2rmjirr-container' geo:returnData='$returnData'> 

    // Страна
    <div class='el2rmjirr-block' >    
        <label>Страна</label>
        $select = new tmp_helper_select();
        $select->attr("class","el2rmjirr-country el2rmjirr-select");
        if(!$country->title())
            $select->option("","Выберите страну");    
        foreach($countries->limit(0) as $item)
            $select->option($item->id(),$item->title());    
        $select->selected($country->id());
        $select->exec();
    </div>
    
    // Регион
    <div class='el2rmjirr-block' >
        <label>Регион</label>
        $select = new tmp_helper_select();
        $select->attr("class","el2rmjirr-region el2rmjirr-select");
        foreach($country->regions()->limit(0) as $item)
            $select->option($item->id(),$item->title());
        $select->selected($region->id());
        $select->exec();    
    </div>
    
    // Городs
    <div class='el2rmjirr-block' >
        <label>Город</label>
        $select = new tmp_helper_select();
        $select->attr("class","el2rmjirr-city el2rmjirr-select");
        foreach($region->cities()->limit(0) as $item)
            $select->option($item->id(),$item->title());
        $select->selected($city->id());
        $select->exec();
    </div>
    
    //Поля которые передаются на сервер
    <input type='hidden' name='country' value='{$country->$returnData()}'>
    <input type='hidden' name='region' value='{$region->$returnData()}'>
    <input type='hidden' name='city' value='{$city->$returnData()}'>
</div>