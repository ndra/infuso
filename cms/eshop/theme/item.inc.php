<?

tmp::param("header",$p1->title());

tmp::add("center","eshop:item.photos",$p1);
tmp::add("center","eshop:layout.buy",$p1);
tmp::add("center","eshop:item.description",$p1);
tmp::add("center","eshop:item.attributes",$p1);
tmp::add("center","eshop:item.similar",$p1);
tmp::add("center","eshop:item.alsoBuy",$p1);
tmp::add("right","eshop:layout.subgroups",$p1->group()->level0());
tmp::exec("eshop:layout");

?>