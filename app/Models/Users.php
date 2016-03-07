<?php
namespace Models;
use Resources;

class Users extends Resources\ActiveRecord
{
    
    public function __construct()
    {   
        call_user_func_array( 'parent::__construct', func_get_args() );    
        $this->db = new Resources\Database;
    }

    public function getUserByUsername($username)
    {
    	return $this->db->row("SELECT * FROM users 
            WHERE username = '{$username}'");
    }
}
