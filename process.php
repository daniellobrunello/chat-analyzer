<?php
header('Content-type: application/json; charset=utf-8');

mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('memory_limit','4000M');

/** These values do not work */
/* You have to set these values in .htaccess or the php ini file */
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '101M');

include_once('db.php');
include_once('stopwords.php');
include_once('unicode_emojis.php');

$separator_set = [
    "android" => [
        "newline_regex" => "/[0-9]{2}\.[0-9]{2}\.[0-9]{2},\s[0-9]{2}:[0-9]{2}/",
        "date_time_sep" => ", ",
        "time_name_sep" => " - ",
        "name_message_sep" => ": ",
        "media_message" => "/(<medien ausgeschlossen>)/"
    ],
    "ios" => [
        "newline_regex" => "/[0-9]{2}\.[0-9]{2}\.[0-9]{2},\s[0-9]{2}:[0-9]{2}/",
        "date_time_sep" => ", ",
        "time_name_sep" => "] ",
        "name_message_sep" => ": ",
        "media_message" => "/(bild|audio|video|gif)\sweggelassen/"
    ]
];
$guid = bin2hex(openssl_random_pseudo_bytes(16));
$uploadDir = 'uploaded-chats/';

$encryptDB = new encryptDB();

function logProcessing($msg) {
    $pre = "[" . date('Y-m-d H:i:s') . "] ";
    file_put_contents('logs/process.log', $pre . $msg . PHP_EOL, FILE_APPEND);
}

logProcessing("Start of GUID $guid");

function encrypt_and_save_chat($plaintext, $guid) {
    global $encryptDB;

    $cipher = "aes-128-gcm";
    $key = bin2hex(openssl_random_pseudo_bytes(16));

    $ciphertext = $plaintext;
    
    if (in_array($cipher, openssl_get_cipher_methods())) {
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $digested_key = openssl_digest($key, "sha256");
        $ciphertext = openssl_encrypt($plaintext, $cipher, $digested_key, $options=0, $iv, $tag);
    } else {
        die('Cipher not possible');
    }

    $details = [
        "guid" => $guid,
        "cipher" => $cipher,
        "iv" => $iv,
        "tag" => $tag,
    ];

    // file_put_contents($dir, $ciphertext);
    $encryptDB->saveEncryptionDetails($details);
    $encryptDB->saveEncryptedChat($guid, $ciphertext);

    return $key;
}

function convert_date_format($old_date = ''){
    $old_date = trim($old_date);

    if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $old_date)) { // MySQL-compatible YYYY-MM-DD format	
        $new_date = $old_date;
    } elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $old_date)) { // DD-MM-YYYY format
        $new_date = substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);

    } elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{2}$/', $old_date)) { // DD-MM-YY format	
        $new_date = '20' . substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);

    } elseif (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{2}$/', $old_date)) { // DD.MM.YY format
        $new_date = '20' . substr($old_date, 6, 4) . '-' . substr($old_date, 3, 2) . '-' . substr($old_date, 0, 2);

    } else {
        $new_date = '0000-00-00';

    }

    return $new_date;
}


function convert_time_format($old_time = ''){
    $old_time = trim($old_time);
    if (preg_match('/^[0-9]{2}:[0-9]{2}$/', $old_time)) { // HH:mm
        $new_time = $old_time . ':00';

    } elseif (preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $old_time)) { // HH:mm
            $new_time = $old_time;
    } else {
        $new_time = '00:00:00';
    }
    
    return $new_time;
}





try {
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['upfile']['error']) ||
        is_array($_FILES['upfile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    // Check $_FILES['upfile']['error'] value.
    switch ($_FILES['upfile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
            throw new RuntimeException('Exceeded filesize limit. (INI)');
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit. (FORM)');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // You should also check filesize here. // 10M
    if ($_FILES['upfile']['size'] > 10485760) {
        throw new RuntimeException('Exceeded filesize limit. (ManCheck)');
    }

    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['upfile']['tmp_name']);
    if (false === $ext = array_search(
        $mime,
        array(
            'csv' => 'text/csv',
            'txt' => 'text/plain'
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format: ' . $mime);
    }

    // You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    $uploadDate = date('Y-m-d');
    $fname = $guid . "." . $ext;
    $uploadfile = $uploadDir . basename($fname);
    if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadfile)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    //$DBcon->insertImage($fid, $user, $fname);
    //echo json_encode($uploadfile);

} catch (RuntimeException $e) {
    echo $e->getMessage();
    die();
}
// File upload complete.


// $uploadfile = $uploadDir . '9a9daaee25d0740b36dbdf7f44f36784.txt';
$file = file($uploadfile);

$final = [];
$users = [];
$msgData = [];
$data = [
    "total" => [
        "messages" => 0,
        "chars" => 0,
        "punctuation" => 0,
        "words" => 0,
        "wordLen" => 0,
        "pctPerMsg" => 0,
        "vocabulary" => [],
        "emojis" => 0,
        "emoji_dict" => []
    ]
];


// // dev todo remove
// $guid = '8adf43e0389c2489b4f667f082c1f607';
// $file = file('uploaded-chats/8adf43e0389c2489b4f667f082c1f607.txt');
// // end dev

// check which fileformat is present
// check 10% of lines
$iOSMatcher = "/(\[\d{2}\.\d{2}\.\d{2},\s\d{2}:\d{2}:\d{2}\])(\s.*:\s)(.*)/";
$androidMatcher = "/(\d{2}\.\d{2}\.\d{2},\s\d{2}:\d{2})(\s-\s.*:)(\s.*)/";
$lineNumbers = count($file);
$linesToCheck = ceil($lineNumbers * 0.1);
$iosLines = 0;
$androidLines = 0;
for ($i = 0; $i < $linesToCheck; $i++) {
    $l = rand(0, $lineNumbers - 1);
    $line = $file[$l];

    if (preg_match($iOSMatcher, $line)) {
        $iosLines += 1;
    }

    if (preg_match($androidMatcher, $line)) {
        $androidLines += 1;
    }
}

logProcessing("Found $androidLines Android lines and $iosLines iOS lines");
if ($androidLines > $iosLines) {
    $separators = $separator_set["android"];
    logProcessing("Probably Android!");
} else {
    $separators = $separator_set["ios"];
    logProcessing("Probably iOS!");
} 


$fullRows = [];
foreach($file as $idx => $line) {
    $line = trim($line, "\n");
    $line = trim($line, " ");
    
    $isNewLine = preg_match($separators["newline_regex"], $line);
    
    if (!$isNewLine) {
        $fullRows[count($fullRows) - 1] = $fullRows[count($fullRows) - 1] . ' ' . $line;
    } else {
        $fullRows[] = $line;
    }
}

foreach($fullRows as $idx => $row) {
    $split = explode($separators["date_time_sep"], $row, 2);
    $date = ltrim($split[0], "[");
    $split = explode($separators["time_name_sep"], $split[1], 2);
    $time = $split[0];
    $split = explode($separators["name_message_sep"], $split[1], 2);
    try {
        $who = $split[0];

        if (!isset($split[1])) continue;
        $msg = $split[1];
    } catch (\Throwable $th) {
        continue;
    }


    // $who = strtolower($who);

    if (preg_match('/(hat\sden\sbetreff\svon.*)/', strtolower($who))) continue;

    $date = convert_date_format($date);
    $time = convert_time_format($time);

    if (!in_array($who, $users)) {
        $users[] = $who;

        $data[$who] = [
            "messages" => 0,
            "chars" => 0,
            "punctuation" => 0,
            "words" => 0,
            "wordLen" => 0,
            "pctPerMsg" => 0,
            "vocabulary" => [],
            "emojis" => 0,
            "emoji_dict" => []
        ];
    }

    if (preg_match($separators["media_message"], strtolower($msg))) {
        continue;
    }

    preg_match_all( '/[\x{' . implode( '}\x{', $unicodes ) . '}]/u', $msg, $emojis);
    $emojis = $emojis[0];

    $msg = strtolower($msg);

    preg_match_all('/,|\.|:|\!|\?|\+/', $msg, $puncts);
    $puncts = $puncts[0];

    $words = preg_split('/\W+/u', $msg, -1, PREG_SPLIT_NO_EMPTY);
    $words = array_map("strtolower", $words);
    
    $data[$who]["messages"] += 1;
    $data[$who]["chars"] += strlen($msg);
    $data[$who]["punctuation"] += count($puncts);
    $data[$who]["words"] += count($words);
    $data[$who]["emojis"] += count($emojis);

    $data["total"]["messages"] += 1;
    $data["total"]["chars"] += strlen($msg);
    $data["total"]["punctuation"] += count($puncts);
    $data["total"]["words"] += count($words);
    $data["total"]["emojis"] += count($emojis);
    
    
    $data[$who]["wordLen"] = ($data[$who]["chars"] - $data[$who]["punctuation"]) / $data[$who]["words"];
    $data["total"]["wordLen"] = ($data["total"]["chars"] - $data["total"]["punctuation"]) / $data["total"]["words"];

    $data[$who]["wordsPerMsg"] = $data[$who]["words"] / $data[$who]["messages"];
    $data["total"]["wordsPerMsg"] = $data[$who]["words"] / $data[$who]["messages"];

    $data[$who]["pctPerMsg"] = $data[$who]["punctuation"] / $data[$who]["messages"];
    $data["total"]["pctPerMsg"] = $data[$who]["punctuation"] / $data[$who]["messages"];
    
    foreach($words as $word) {
        $word = strval($word);
        $word = trim($word);

        if (in_array($word, $stopWords)) {
            continue;
        }

        if (strlen($word) < 2) {
            continue;
        }

        if (strval(intval($word)) === $word) {
            continue;
        }

        if (!array_key_exists($word, $data[$who]["vocabulary"])) {
            $data[$who]["vocabulary"][$word] = 0;
        }

        if (!array_key_exists($word, $data["total"]["vocabulary"])) {
            $data["total"]["vocabulary"][$word] = 0;
        }
         
        $data[$who]["vocabulary"][$word] += 1;
        $data["total"]["vocabulary"][$word] += 1;
    }

    foreach($emojis as $emoji) {

        $emoji = json_encode($emoji);
        if (strlen($emoji) < 6) {
            continue;
        }

        if (!array_key_exists($emoji, $data[$who]["emoji_dict"])) {
            $data[$who]["emoji_dict"][$emoji] = 0;
        }

        if (!array_key_exists($emoji, $data["total"]["emoji_dict"])) {
            $data["total"]["emoji_dict"][$emoji] = 0;
        }

        $data[$who]["emoji_dict"][$emoji] += 1;
        $data["total"]["emoji_dict"][$emoji] += 1;
    }

    ###
    ### USER STATS DONE
    ###

    $msgData[] = [
        "date" => $date . ' ' . $time,
        "who" => $who,
        "msg" => $words
    ];
}


foreach($users as $who) {
    $data[$who]["wordLen"] = round(($data[$who]["chars"] - $data[$who]["punctuation"]) / $data[$who]["words"], 2);
    $data[$who]["wordsPerMsg"] = round($data[$who]["words"] / $data[$who]["messages"], 2);
    $data[$who]["pctPerMsg"] = round($data[$who]["punctuation"] / $data[$who]["messages"], 2);
    
    arsort($data[$who]["vocabulary"]);
    arsort($data[$who]["emoji_dict"]);
    $data[$who]["vocabulary"] = array_slice($data[$who]["vocabulary"], 0, 100);
    $data[$who]["emoji_dict"] = array_slice($data[$who]["emoji_dict"], 0, 100);
}

$data["total"]["wordLen"] = round(($data["total"]["chars"] - $data["total"]["punctuation"]) / $data["total"]["words"], 2);
$data["total"]["wordsPerMsg"] = round($data[$who]["words"] / $data[$who]["messages"], 2);
$data["total"]["pctPerMsg"] = round($data[$who]["punctuation"] / $data[$who]["messages"], 2);
arsort($data["total"]["vocabulary"]);
arsort($data["total"]["emoji_dict"]);
$data["total"]["vocabulary"] = array_slice($data["total"]["vocabulary"], 0, 100);
$data["total"]["emoji_dict"] = array_slice($data["total"]["emoji_dict"], 0, 100);


$final = [
    "users" => $users,
    "msgData" => $msgData,
    "userData" => $data
];


$jsonEncoded = json_encode($final, JSON_UNESCAPED_UNICODE);
$key = encrypt_and_save_chat($jsonEncoded, $guid);
//unlink($uploadfile);

$response = [
    "success" => true,
    "message" => "Success!",
    "guid" => $guid,
    "pass" => $key
];

echo json_encode($response);
logProcessing("End of GUID $guid");

?>
