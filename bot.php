<?php
define('API_KEY', 'YOUR_BOT_TOKEN'); //add token
define('ADMIN_ID', 'ADMIN_ID'); //admin id
// main function
function Bot($Method, $Datas = [])
{
    $Url = "https://api.telegram.org/bot" . API_KEY . "/" . $Method;
    $Ch = curl_init();
    curl_setopt($Ch, CURLOPT_URL, $Url);
    curl_setopt($Ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($Ch, CURLOPT_POSTFIELDS, $Datas);
    $Res = curl_exec($Ch);
    if (curl_error($Ch)) {
        var_dump(curl_error($Ch));
    } else {
        return json_decode($Res);
    }
}

// functions
function SendMessage($ChatId, $Text)
{
    Bot(
        'sendMessage',
        [
            'chat_id' => $ChatId,
            'text' => $Text,
            'parse_mode' => 'MarkDown'
        ]
    );
}

function DeleteMessage($ChatId, $MessageId)
{
    Bot('deleteMessage', [
        'chat_id' => $ChatId,
        'message_id' => $MessageId
    ]);
}

function SendChatAction($ChatId, $Action)
{
    Bot('sendChatAction', [
        'chat_id' => $ChatId,
        'action' => $Action
    ]);
}
function RestrictChatMember($ChatId, $UserId)
{
    Bot('restrictChatMember', [
        'chat_id' => $ChatId,
        'user_id' => $UserId,
        // user permissions
        'can_send_messages' => false,
        'can_send_media_messages' => false,
        'can_send_polls' => false,
        'can_send_other_messages' => false,
        'can_add_web_page_previews' => false,
        'can_change_info' => true,
        'can_invite_users' => true,
        'can_pin_messages' => false,
        // user restriction time
        'until_date' => time() + (60*60*24), // until date based on hours from current time (like 24 hours)

    ]);
}
// variables
@mkdir("data");
$Update = json_decode(file_get_contents('php://input'));
$UserId = $Update->message->from->id;
$FirstName = $Update->message->from->first_name;
$LastName = $Update->message->from->last_name;
$UserName = $Update->message->from->username;
$ChatId = isset($Update->callback_query->message->chat->id)?$Update->callback_query->message->chat->id:$Update->message->chat->id;
$Message = $Update->message;
$Text = $Message->text;
$Caption = $Message->caption;
$MessageId = $Message->message_id;
$Tci = $Update->message->chat->type;
$UsersList = file_get_contents("data/users.txt");
$File = json_decode(file_get_contents("data/$ChatId.json"),true);
$Step = $File["userinfo"]["step"];
// start
if ($Text == "/start" and $Tci == "private") {
    $UsersExplode = explode("\n",$UsersList);
        if(!in_array($ChatId, $UsersExplode)){
            $AddUsers = $ChatId."\n";
            file_put_contents("data/users.txt",$AddUsers,FILE_APPEND);
        }
        // set step
        $Data["userinfo"]["step"]= "start";
        $Data = json_encode($Data,true);
        file_put_contents("data/$ChatId.json",$Data);
        SendChatAction($ChatId, "typing");
        SendMessage($ChatId, "hi,\n this is start message");
} elseif ($Text == "/sendmessage" and $Tci == "private" and $ChatId == ADMIN_ID){
    // set step
    $Data["userinfo"]["step"]= "sendmessage";
    $Data = json_encode($Data,true);
    file_put_contents("data/$ChatId.json",$Data);
    SendChatAction($ChatId, "typing");
    SendMessage($ChatId, "Please send your message");
}elseif (!empty($Text) and $Step == "sendmessage" and $Tci == "private" and $ChatId == ADMIN_ID){
    $path = "data/users.txt";
    $file = fopen($path, 'r');
    $data = fread($file, filesize($path));
    fclose($file);
    $lines =  explode("\n",$data);
    foreach ($lines as $line){
        SendMessage($line, $Text);
    }
}else {
    // bad words
    require ("badwords.php");
    $Bwd = count($BadWords);
    for ($i = 0; $i < $Bwd; $i++) {
        if ((strstr(strtolower($Text), $BadWords[$i]) or strstr(strtolower($Caption), $BadWords[$i])) and ($Tci == "group" or $Tci == "supergroup")) {
            DeleteMessage($ChatId, $MessageId);
            SendChatAction($ChatId, "typing");
            SendMessage($ChatId, "User $FirstName $LastName / @$UserName limited!");
            RestrictChatMember($ChatId, $UserId);
            die();
        }
    }
}