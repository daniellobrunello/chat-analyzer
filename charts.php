<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Content-Type: text/html');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no cache');

mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit','4000M');

session_cache_limiter('nocache');
session_start();


include_once('db.php');
$encryptDB = new encryptDB();


if (isset($_GET["chat"])) {
    $chatId = filter_input(INPUT_GET, "chat", FILTER_SANITIZE_STRING);
    // todo Use this later
} else {
    die('No chat ID given!');
}

if (isset($_GET["pass"])) {
    // todo Use this later
    $chatKey = filter_input(INPUT_GET, "pass", FILTER_SANITIZE_STRING);
} else {
    die('No password given');
}


function decrypt_chat_content($guid, $key) {
    global $encryptDB;

    $encrypted_chat = $encryptDB->getEncryptedChat($guid);
    if ($encrypted_chat === FALSE) {
        die('Chat does not exist');
    }

    $encrypted_content = $encrypted_chat["content"];

    $details = $encryptDB->getEncryptionDetails($guid);
    if ($details === FALSE || !isset($details)) {
        die('Decryption details not found');
    }
    
    $cipher = $details["cipher"];
    $iv = $details["iv"];
    $tag = $details["tag"];

    if (in_array($cipher, openssl_get_cipher_methods())) {
        $digested_key = openssl_digest($key, "sha256");
        $plaintext = openssl_decrypt($encrypted_content, $cipher, $digested_key, $options=0, $iv, $tag);
    } else {
        die('Cipher method not available on this server');
    }

    return $plaintext;
}

// $encrypted_content = file_get_contents($filePathAndName);
$decrypted_content = decrypt_chat_content($chatId, $chatKey);

if ($decrypted_content === FALSE) {
    die('Decryption not successful');
}

$content = json_decode($decrypted_content);
$users = $content->users;

$hoursOfTheDay = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
$weekDays = ["Mo.", "Di.", "Mi.", "Do.", "Fr.", "Sa.", "So."];
$months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];



/***********************************************************************
*                   Prepare hourly chat volume
/**********************************************************************/
$hourly_chat_volume = [];
$hourly_chat_volume_template = [];
$hourly_chat_volume_x_vals = $hoursOfTheDay;
for($i = 1; $i < 25; $i++) {
    $hourly_chat_volume_template[] = 0;
}


/***********************************************************************
*                   Prepare daily chat volume
/**********************************************************************/
$daily_chat_volume = [];
$daily_chat_volume_template = [];
$daily_chat_volume_x_vals = $weekDays;
for($i = 1; $i < 8; $i++) {
    $daily_chat_volume_template[] = 0;
}


/***********************************************************************
*                   Prepare monthly chat volume
/**********************************************************************/
$monthly_chat_volume = [];
$monthly_chat_volume_template = [];
$monthly_chat_volume_x_vals = $months;
for($i = 1; $i < 13; $i++) {
    $monthly_chat_volume_template[] = 0;
}


/***********************************************************************
*                   Prepare weekday daytime heatmap
/**********************************************************************/
$daytime_weekday_heatmap = [
    "z" => [],
    "x" => $hoursOfTheDay,
    "y" => $weekDays,
    "type" => "heatmap",
];
foreach($weekDays as $idxx => $weekday) {
    $daytime_weekday_heatmap["z"][] = [];
    foreach($hoursOfTheDay as $idxy => $hour) {
        $daytime_weekday_heatmap["z"][$idxx][$idxy] = 0;
    }
}


/***********************************************************************
*                       Prepare response heatmap
/**********************************************************************/
$response_heatmap = [
    "z" => [],
    "x" => [],
    "y" => [],
    "type" => "heatmap",
];
$previousUser = NULL;
$userIndex = [];

foreach($users as $idxx => $ux) {
    $response_heatmap["x"][] = $ux;
    $response_heatmap["y"][] = $ux;
    $response_heatmap["z"][] = [];
    $userIndex[$ux] = $idxx;

    foreach($users as $idxy => $uy) {
        $response_heatmap["z"][$idxx][$idxy] = 0;
    }
}


/***********************************************************************
*                       Other Stats
/**********************************************************************/
$stats = [
    "firstMessage" => NULL,
    "lastMessage" => NULL,
];

/***********************************************************************
*                   C A L C U L A T E    S T A T S
/**********************************************************************/
foreach($content->msgData as $idx => $msg) {

    $date = new DateTime($msg->date);
    $who = $msg->who;
    $msgWords = $msg->msg;

    $hour = intval($date->format('H'));
    $minute = intval($date->format('i'));
    $weekday = $date->format('N');
    $month = intval($date->format('m'));


    if ($minute > 30) {
        $hour = ($hour + 1) % 24;
    }

    //
    // First Message
    if ($stats["firstMessage"] == NULL) {
        $stats["firstMessage"] = [$who, $date->format('Y-m-d H:i:s')];
    }

    //
    // Last Message
    $stats["lastMessage"] = [$who, $date->format('Y-m-d H:i:s')];

    //
    // Calculate hourly chat volume
    if (!array_key_exists($who, $hourly_chat_volume)) {
        $hourly_chat_volume[$who] = [];
        $hourly_chat_volume[$who]["name"] = $who;
        $hourly_chat_volume[$who]["line"] = ["shape" => "spline"];
        $hourly_chat_volume[$who]["mode"] = "lines";
        $hourly_chat_volume[$who]["x"] = $hourly_chat_volume_x_vals;
        $hourly_chat_volume[$who]["y"] = $hourly_chat_volume_template;
    }

    $hourly_chat_volume[$who]["y"][$hour] += 1;

    //
    // Calculate daily volume
    if (!array_key_exists($who, $daily_chat_volume)) {
        $daily_chat_volume[$who] = [];
        $daily_chat_volume[$who]["name"] = $who;
        $daily_chat_volume[$who]["line"] = ["shape" => "spline"];
        $daily_chat_volume[$who]["mode"] = "lines";
        $daily_chat_volume[$who]["x"] = $daily_chat_volume_x_vals;
        $daily_chat_volume[$who]["y"] = $daily_chat_volume_template;
    }

    $daily_chat_volume[$who]["y"][$weekday - 1] += 1;


    //
    // Calculate monthly volume
    if (!array_key_exists($who, $monthly_chat_volume)) {
        $monthly_chat_volume[$who] = [];
        $monthly_chat_volume[$who]["name"] = $who;
        $monthly_chat_volume[$who]["line"] = ["shape" => "spline"];
        $monthly_chat_volume[$who]["mode"] = "lines";
        $monthly_chat_volume[$who]["x"] = $monthly_chat_volume_x_vals;
        $monthly_chat_volume[$who]["y"] = $monthly_chat_volume_template;
    }

    $monthly_chat_volume[$who]["y"][$month - 1] += 1;


    //
    // Calculate weekday daytime heatmap
    $daytime_weekday_heatmap["z"][$weekday - 1][$hour] += 1;


    //
    // Calculate response heatmap
    if ($previousUser !== NULL) {
        $idx1 = $userIndex[$who];
        $idx2 = $userIndex[$previousUser];
        if ($idx1 !== $idx2) { // don't count monologue
            $response_heatmap["z"][$idx1][$idx2] += 1;
    
        }
    }
    $previousUser = $who;

}



$userStatsCounter = 0;
foreach((array) $content->userData as $idx => $data) {
    if ($idx == "total") continue;

    if ($userStatsCounter == 0) {
        $user_stats_pie_charts = [
            [
                "values" => [$data->messages],
                "labels" => [$idx],
                "type" => "pie",
                "name" => "Nachrichten",
                "domain" => ["row" => 0, "column" => 0]
            ], [
                "values" => [$data->words],
                "labels" => [$idx],
                "type" => "pie",
                "name" => "Wörter",
                "domain" => ["row" => 0, "column" => 1]
            ], [
                "values" => [$data->chars],
                "labels" => [$idx],
                "type" => "pie",
                "name" => "Zeichen",
                "domain" => ["row" => 1, "column" => 0]
            ], [
                "values" => [$data->punctuation],
                "labels" => [$idx],
                "type" => "pie",
                "name" => "Interpunktion",
                "domain" => ["row" => 1, "column" => 1]
            ]
        ];
    } else {
        $user_stats_pie_charts[0]["values"][] = $data->messages;
        $user_stats_pie_charts[0]["labels"][] = $idx;
        $user_stats_pie_charts[1]["values"][] = $data->words;
        $user_stats_pie_charts[1]["labels"][] = $idx;
        $user_stats_pie_charts[2]["values"][] = $data->chars;
        $user_stats_pie_charts[2]["labels"][] = $idx;
        $user_stats_pie_charts[3]["values"][] = $data->punctuation;
        $user_stats_pie_charts[3]["labels"][] = $idx;
    }
    
    $userStatsCounter += 1;
}

// Finalize data
$hourly_chat_volume = array_values($hourly_chat_volume);
$daily_chat_volume = array_values($daily_chat_volume);
$monthly_chat_volume = array_values($monthly_chat_volume);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">

    <link rel="shortcut icon" type="image/png" href="favicon.png"/>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/bulma.css">
    <link rel="stylesheet" href="css/style.css">
    
    <title>WhatsApp Chat Analyzer Charts</title>
</head>
<body>

<?php 

echo "<script>\n";

echo 'var user_stats_pie_charts = ' . json_encode($user_stats_pie_charts) . ';';
echo "\n";

echo 'var hourly_chat_volume = ' . json_encode($hourly_chat_volume) . ';';
echo "\n";

echo 'var daily_chat_volume = ' . json_encode($daily_chat_volume) . ';';
echo "\n";

echo 'var monthly_chat_volume = ' . json_encode($monthly_chat_volume) . ';';
echo "\n";

echo 'var daytime_weekday_heatmap = ' . json_encode([$daytime_weekday_heatmap]) . ';';
echo "\n";

echo 'var response_heatmap = ' . json_encode([$response_heatmap]) . ';';
echo "\n</script>";
?>


<div id="page-wrap">
    <div class="section" style="padding-top:1rem">
        <div id="upload-wrap" class="container">
            
            <div id="message-wrapper" class="columns">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="message" class="notification">
                        <button class="delete"></button>
                        <span id="message-text"></span>
                    </div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <h1 class="title is-centered">WhatsApp Chat Analyzer</h1>
                    <h2 class="subtitle">Your results</h2>
                </div>
                <div class="column"></div>
            </div>

            <br>
            <br>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div>
                    <?php 
                        echo "Von <strong>" .  $stats["firstMessage"][1] . "</strong> (von " . $stats["firstMessage"][0] . ") ";
                        echo "bis <strong>" .  $stats["lastMessage"][1] . "</strong> (von " . $stats["lastMessage"][0] . ").";
                    ?>
                    </div>
                    <div>
                    <?php
                        echo "<strong>" . number_format($content->userData->total->messages, 0, ",", ".") . "</strong> Nachrichten<br>"; 
                        echo "<strong>" . number_format($content->userData->total->words, 0, ",", ".") . "</strong> Wörter<br>";
                        echo "<strong>" . number_format($content->userData->total->chars, 0, ",", ".") . "</strong> Zeichen<br>";
                        echo "<strong>" . number_format($content->userData->total->punctuation, 0, ",", ".") . "</strong> Interpunktionen<br>";
                        echo "Durchschnittlich <strong>" . number_format($content->userData->total->wordLen, 2, ",", ".") . "</strong> Zeichen pro Wort<br>";
                        echo "Durchschnittlich <strong>" . number_format($content->userData->total->pctPerMsg, 2, ",", ".") . "</strong> Interpunktionen pro Nachricht<br>";
                    ?>
                    </div>
                    <br>
                    <h2>Top 20 Wörter</h2>
                    <div>
                    <?php 
                        $top20Count = 1;
                        $favWords = (array) $content->userData->total->vocabulary;
                        foreach($favWords as $word => $count) {
                            if ($top20Count > 20) break;

                            echo $top20Count . ". " . $word . " (" . $count . "x)<br>";
                            $top20Count += 1;
                        }
                    ?>
                    </div>
                    <br>
                    <h2>Top 20 Emojis</h2>
                    <div>
                    <?php 
                        $top20Count = 1;
                        $favEmojis = (array) $content->userData->total->emoji_dict;
                        foreach($favEmojis as $emoji => $count) {
                            if ($top20Count > 20) break;

                            echo $top20Count . ". " . json_decode($emoji) . " (" . $count . "x)<br>";
                            $top20Count += 1;
                        }
                        
                    ?>
                    </div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="user-text-stats"></div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="chat-volume-hourly"></div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="chat-volume-daily"></div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="chat-volume-monthly"></div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="weekday-daytime-heatmap"></div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-two-thirds">
                    <div id="response-heatmap"></div>
                </div>
                <div class="column"></div>
            </div>
            
            <!-- 
            <div class="columns">
                <div class="column"></div>
                <div class="column is-half">
                    <h1 class="title is-centered">WhatsApp Chat Analyzer</h1>
                    <h2 class="subtitle">Upload your chat history and get exciting insights!</h2>
                </div>
                <div class="column"></div>
            </div>

            <br>
            <br>

            <form id="upload-form" action="" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                <div class="columns">
                    <div class="column"></div>
                    <div class="column">
                        <div class="field">
                            <div class="file is-centered is-boxed is-info has-name">
                                <label class="file-label">
                                <input id="upload-file" class="file-input" type="file" name="upfile">
                                <span class="file-cta">
                                    <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Select chat file
                                    </span>
                                </span>
                                <span id="filename" class="file-name">Please choose file</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="column"></div>
                </div>

                <br>

                <div class="columns">
                    <div class="column"></div>
                    <div class="column">
                        <a id="upload-btn" class="button is-success is-fullwidth is-large" disabled>Upload</a>
                        <div id="progress-bar">
                            <progress class="progress is-primary" value="15" max="100"></progress>
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
            </form>
        </div>
    </div> -->

    <div class="footer">
        <div class="container">
            <div class="columns">
                <div class="column"></div>
                <div class="column" style="text-align: center;">
                    &copy; <!--2019 bruness.org-->
                </div>
                <div class="column"></div>
            </div>
        </div>
    </div>

</div>


<script src="js/jq.js"></script>
<script src="js/plotly.js"></script>
<script src="js/chartmain.js"></script>
</body>
</html>