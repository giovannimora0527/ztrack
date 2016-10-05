<?php

class Ruta extends Eloquent {

    protected $connection = 'gs';
    protected $table = 'gs_user_routes';
    protected $fillable = array('route_id','user_id', 'group_id', 'route_name', 'route_color', 'route_visible', 'route_name_visible', 'route_deviation', 'route_points');

}
