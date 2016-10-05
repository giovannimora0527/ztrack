<?php

class Grupo extends Eloquent {
    
    protected $connection = 'gs';    
    protected $table = 'gs_user_object_groups';      
    protected $fillable = array('group_id','user_id', 'group_name', 'group_desc');
    
}

