<?php
define('API_KEY', 'Your_Token'); //add_token
// main function
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}

// functions
function SendMessage($ChatId, $Text)
{
    bot('sendMessage', [
            'chat_id' => $ChatId,
            'text' => $Text,
            'parse_mode' => 'MarkDown']
    );
}

function DeleteMessage($ChatId, $MessageId)
{
    bot('deleteMessage', [
        'chat_id' => $ChatId,
        'message_id' => $MessageId
    ]);
}

function sendChatAction($ChatId, $Action)
{
    bot('sendChatAction', [
        'chat_id' => $ChatId,
        'action' => $Action
    ]);
}
function restrictChatMember($ChatId, $UserId)
{
    bot('restrictChatMember', [
        'chat_id' => $ChatId,
        'user_id' => $UserId,
        'can_send_messages' => false,
        'can_send_media_messages' => false,
        'can_send_polls' => false,
        'can_send_other_messages' => false,
        'can_add_web_page_previews' => false,
        'can_change_info' => false,
        'can_invite_users' => false,
        'can_pin_messages' => false,
        'until_date' => time()+86400, // until date for 24 hour from current time

    ]);
}
// variables
$Update = json_decode(file_get_contents('php://input'));
$UserId = $Update->message->from->id;
$FirstName = $Update->message->from->first_name;
$LastName = $Update->message->from->last_name;
$UserName = $Update->message->from->username;
$Message = $Update->message;
$ChatId = $Message->chat->id;
$Text = $Message->text;
$Caption = $Message->caption;
$MessageId = $Message->message_id;
$Tci = $Update->message->chat->type;

// start
if ($Text == "/start" and $Tci == "private") {
    sendChatAction($ChatId, "typing");
    SendMessage($ChatId, "hi,\n this is start message");
} else {
    // bad words
    $BadWords = ['تلگرام ضد فیلتر','تلگرام بدون فیلتر'];
    $Bwd = count($BadWords);
    for ($i = 0; $i < $Bwd; $i++) {
        if ((strstr(strtolower($Text), $BadWords[$i]) or strstr(strtolower($Caption), $BadWords[$i])) and ($Tci == "group" or $Tci == "supergroup")) {
            DeleteMessage($ChatId, $MessageId);
            sendChatAction($ChatId, "typing");
            SendMessage($ChatId, "User $FirstName $LastName / @$UserName limited!");
            restrictChatMember($ChatId,$UserId);
            die();
        }
    }
}