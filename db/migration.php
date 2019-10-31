<?php


function cli_migration()
{
    include __DIR__.'/ChatsDB.php';
    include __DIR__.'/../config.php';

    $servername = DB_ADDRESS . ":" . DB_PORT;
    $db =  mysqli_connect($servername, DB_USERNAME, DB_PASSWORD, DB_NAME);
}


if(defined('STDIN') ) 
{
    cli_migration();
}
else 
{

}
