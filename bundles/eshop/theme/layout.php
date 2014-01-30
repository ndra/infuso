<?

tmp::jq();
tmp::js("/mod/res/core.js");

tmp::add("right","/eshop/layout/cart");
tmp::add("right","/eshop/layout/myOrders");
tmp::add("right","/eshop/layout/myHistory");

tmp::exec("tmp:layout");