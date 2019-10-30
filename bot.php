<?php
include __DIR__ . '/autoload.php';

// main function
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
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
function sendmessage($chatid, $text)
{
    bot(
        'sendMessage',
        [
            'chat_id' => $chatid,
            'text' => $text,
            'parse_mode' => 'MarkDown'
        ]
    );
}

function deletemessage($chatid, $messageid)
{
    bot('deleteMessage', [
        'chat_id' => $chatid,
        'message_id' => $messageid
    ]);
}

function sendchataction($chatid, $action)
{
    bot('sendChatAction', [
        'chat_id' => $chatid,
        'action' => $action
    ]);
}

function restrictchatmember($chatid, $userid)
{
    bot('restrictChatMember', [
        'chat_id' => $chatid,
        'user_id' => $userid,
        'can_send_messages' => false,
        'can_send_media_messages' => false,
        'can_send_polls' => false,
        'can_send_other_messages' => false,
        'can_add_web_page_previews' => false,
        'can_change_info' => true,
        'can_invite_users' => true,
        'can_pin_messages' => false,
        'until_date' => time() + (60 * 60 * RESTRICT_TIME),

    ]);
}

// variables
@mkdir("data");
$update = json_decode(file_get_contents('php://input'));
if (!$update) {
    die();
}
$userid = $update->message->from->id;
$firstname = $update->message->from->first_name;
$lastname = $update->message->from->last_name;
$username = $update->message->from->username;
$chatid = isset($update->callback_query->message->chat->id) ? $update->callback_query->message->chat->id : $update->message->chat->id;
$message = $update->message;
$text = $message->text;
$caption = $message->caption;
$messageid = $message->message_id;
$tci = $update->message->chat->type;
$userslist = file_get_contents("data/users.txt");
$file = json_decode(file_get_contents("data/$chatid.json"), true);
$step = $file["userinfo"]["step"];
// start
if ($text == "/start" and $tci == "private") {
    $usersexplode = explode("\n", $userslist);
    if (!in_array($chatid, $usersexplode)) {
        $addUsers = $chatid . "\n";
        file_put_contents("data/users.txt", $addUsers, FILE_APPEND);
    }
    // set step
    $data["userinfo"]["step"] = "start";
    $data = json_encode($data, true);
    file_put_contents("data/$chatid.json", $data);
    sendchataction($chatid, "typing");
    sendmessage($chatid, "hi,\n this is start message");
} elseif ($text == "/sendmessage" and $tci == "private" and $chatid == ADMIN_ID) {
    // set step
    $data["userinfo"]["step"] = "sendmessage";
    $data = json_encode($data, true);
    file_put_contents("data/$chatid.json", $data);
    sendchataction($chatid, "typing");
    sendmessage($chatid, "Please send your message");
} elseif (!empty($text) and $step == "sendmessage" and $tci == "private" and $chatid == ADMIN_ID) {
    // set step
    $data["userinfo"]["step"] = "start";
    $data = json_encode($data, true);
    file_put_contents("data/$chatid.json", $data);
    $path = "data/users.txt";
    $file = fopen($path, 'r');
    $data = fread($file, filesize($path));
    fclose($file);
    $lines = explode("\n", $data);
    foreach ($lines as $line) {
        sendmessage($line, $text);
    }
    sendmessage($chatid, "Your message sent to all users. /start again");
} elseif ($text == "/statistics" and $tci == "private" and $chatid == ADMIN_ID) {
    // set step
    $data["userinfo"]["step"] = "statistics";
    $data = json_encode($data, true);
    file_put_contents("data/$chatid.json", $data);
    $file = "data/users.txt";
    $linecount = -1;
    $handle = fopen($file, "r");
    while (!feof($handle)) {
        $line = fgets($handle);
        $linecount++;
    }

    fclose($handle);

    sendchataction($chatid, "typing");
    sendmessage($chatid, "Number of users is $linecount");
} else {
    // bad words
    require("badwords.php");
    $bwd = count($badwords);
    for ($i = 0; $i < $bwd; $i++) {
        if ((strstr(strtolower($text), $badwords[$i]) or strstr(strtolower($caption), $badwords[$i])) and ($tci == "group" or $tci == "supergroup")) {
            deletemessage($chatid, $messageid);
            sendchataction($chatid, "typing");
            sendmessage($chatid, "User $firstname $lastname / @$username limited!");
            restrictchatmember($chatid, $userid);
            die();
        }
    }
}
