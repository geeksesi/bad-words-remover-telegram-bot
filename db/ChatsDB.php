<?php

class ChatsDB
{
    private $db;

    public function __construct($_db)
    {
        $this->db = $_db;
    }

    public static function migration(string $_charset, string $_prefix)
    {
        $prefix = (substr($_prefix, -1) !== "_") ? $_prefix . "_" : $_prefix;
        $table_name = $prefix . "chats";
        $sql = "CREATE TABLE $table_name (
            id            INT(4)          NOT NULL AUTO_INCREMENT UNSIGNED ,
            chat_id       INT(10)         NOT NULL,
            timestap      BIGINT(12)      NOT NULL UNSIGNED,            
            PRIMARY KEY  (id)
          ) $_charset;";
        return $sql;
    }
}
