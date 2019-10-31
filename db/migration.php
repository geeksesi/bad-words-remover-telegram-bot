<?php


function cli_migration()
{
    include __DIR__ . '/../config.php';
    include __DIR__ . '/ChatsDB.php';
    echo "\nWellcome to Cli migration\n";
    echo "remmember if you want drop a table you must do it manualy\n";
    $servername = DB_ADDRESS . ":" . DB_PORT;
    $db =  mysqli_connect($servername, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($db)
        echo "connect succsessfully to database\n";
    else
        die("can't connect to database");

    if (ChatsDB::migration($db, DB_PREFIX))
        echo "Chats table migration ok\n";
    else
        echo "an error happen while want to make chats table\n" . mysqli_error($db);
}

if (defined('STDIN')) {
    cli_migration();
}
