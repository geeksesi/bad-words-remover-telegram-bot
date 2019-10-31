<?php
//config 
include __DIR__ . '/config.php';

// databases
include __DIR__ . '/db/ChatsDB.php';
include __DIR__ . '/db/DB.php';
// include __DIR__ . '/db/migration.php';

//words list 
include __DIR__ . '/badwords.php';


$db = new DB();
