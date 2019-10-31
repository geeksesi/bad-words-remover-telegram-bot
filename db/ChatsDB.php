<?php

class ChatsDB
{
    private $db;

    public function __construct($_db)
    {
        $this->db = $_db;
    }

    public static function migration($_db, string $_prefix)
    {
        $prefix = (substr($_prefix, -1) !== "_") ? $_prefix . "_" : $_prefix;
        $table_name = $prefix . "chats";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id            INT(4)          UNSIGNED          NOT NULL AUTO_INCREMENT,
            chat_id       INT(10)                           NOT NULL,
            chat_type     VARCHAR(30)                       NOT NULL,
            timestap      BIGINT(12)      UNSIGNED          NOT NULL ,            
            PRIMARY       KEY (id)
            )             CHARSET=utf8;";

        return $_db->query($sql);
    }
}
