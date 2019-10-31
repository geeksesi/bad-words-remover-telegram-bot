<?php

class ChatsDB
{
    private $db;
    public $table_name;
    public function __construct($_db)
    {
        $this->db = $_db;
        $this->table_name = DB_PREFIX . "chats";
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

    public function add(int $_chat_id, string $_chat_type)
    {
        if ($_chat_type !== "group" || $_chat_type !== "“channel”" || $_chat_type !== "“supergroup”" || $_chat_type !== "private") {
            return false;
        }
        $sql_query = "INSERT INTO " . $this->table_name . "(chat_id, chat_type, timestap) VALUES ('" . $_chat_id . "', '" . $_chat_type . "', '" . time() . "')";
        $result = mysqli_query($this->db, $sql_query);
        if (!$result)
            error_log(mysqli_error($this->db));

        return $result;
    }

    public function get(string $_chat_type = null)
    {
        if ($_chat_type !== null) {
            if ($_chat_type !== "group" || $_chat_type !== "“channel”" || $_chat_type !== "“supergroup”" || $_chat_type !== "private") {
                return false;
            }
        }
        $where_query = ($_chat_type === null) ? "" : "WHERE chat_type=" . $_chat_type;
        $sql_query = "SELECT * FROM " . $this->table_name . $where_query;

        $result = mysqli_query($this->db, $sql_query);

        if (!$result)
            error_log(mysqli_error($this->db));

        return $result;
    }
}
