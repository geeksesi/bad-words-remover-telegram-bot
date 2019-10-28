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

// variables
$Update = json_decode(file_get_contents('php://input'));
$Message = $Update->message;
$ChatId = $Message->chat->id;
$Text = $Message->text;
$Caption = $Message->caption;
$MessageId = $Message->message_id;

// start
if ($Text == "/start") {
    sendChatAction($ChatId, "typing");
    SendMessage($ChatId, "hi,\n this is start message");
} else {
    // bad words
    $BadWords = ['تلگرام ضد فیلتر','تلگرام بدون فیلتر'];
    $Bwd = count($BadWords);
    for ($i = 0; $i < $Bwd; $i++) {
        if (strstr(strtolower($Text), $BadWords[$i]) or strstr(strtolower($Caption), $BadWords[$i])) {
            DeleteMessage($ChatId, $MessageId);
            die();
        }
    }
}