<?

/**
 * Класс-поведение для создения отборов и сортировок товаров
 **/ 
class eshop_item_filter extends reflex_filter {

/**
 * Конфигуратор режимов просмотра
 **/ 
public function viewModes() {
    return array(
        array(
            "title" => "Матрица",
            "val" => "grid",
        ),array(
            "title" => "Списком",
            "val" => "list",
        ),
    );
}    
       
/**
 * Конфигуратор режимов сортировки
 * Переопределите эту функцию чтобы настроить режимы просмотра 
 **/ 
public function sortModes() {
    return array(
        array(
            "title" => "По цене",
            "val" => "images",
            "method" => "sort_default",
        ),array(
            "title" => "По цене, начиная с дорогих",
            "val" => "preview",
            "method" => "sort_priceDesc",
        ),
    );
}   

/**
 * Метод, сортирующий товары по цене, начиная с дешевых
 * Сортировка по умолчанию 
 **/ 
public function sort_default() {
    $this->asc("price");
}

/**
 * Метод, сортирующий товары по цене, начиная с дорогих
 **/ 
public function sort_priceDesc() {
    $this->desc("price");
} 

}
