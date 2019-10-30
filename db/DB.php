<?php

class DB
{
    protected $db;
    public $chats;


    public function __construct()
    {
        $servername = DB_ADDRESS . ":" . DB_PORT;
        $this->db =  mysqli_connect($servername, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if (!$this->db) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $this->chats = new ChatsDB($this->db);
    }

    
}
