<?php
/**
 * Кеширование geo_ip
 *
 * @version 0.1
 * @package geo
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
 
class geo_ip_cache extends reflex {
    
    public function all() {
        return reflex::get(get_class());
    }
    
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    public function dataWrappers() {
        return array(
            "ip"    => "mixed/data",
            "country"  => "mixed/data",
            "region"    => "mixed/data",
            "city"    => "mixed/data",
        );
    }
    
    public function reflex_table() {
        return array(
            "name" => "geo_ip_cache",
            "fields" => array(
                array (
                  'name' => 'id',
                  'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ),
                array (
                  'name' => 'ip',
                  'editable' => 1,
                  'type' => "v324-89xr-24nk-0z30-r243",
                  'label' => 'ip',
                  'indexEnabled' => 1,
                ),
                array (
                  'name' => 'country',
                  'editable' => 1,
                  'label' => 'Страна',
                  'type' => "v324-89xr-24nk-0z30-r243",
                ),
                array (
                  'name' => 'region',
                  'editable' => 1,
                  'label' => 'Область',
                  'type' => "v324-89xr-24nk-0z30-r243",
                ),
                array (
                  'name' => 'city',
                  'editable' => 1,
                  'label' => 'Город',
                  'type' => "v324-89xr-24nk-0z30-r243",
                ),
            ),
        );
    }
    
    
}
