<?php
// header stuff?
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
    
    <title>WhatsApp Chat Analyzer</title>
</head>
<body>
<div id="page-wrap">
    <div class="section" style="padding-top:1rem">
        <div id="upload-wrap" class="container">
            
            <div id="message-wrapper" class="columns">
                <div class="column"></div>
                <div class="column is-half">
                    <div id="message" class="notification">
                        <button class="delete"></button>
                        <span id="message-text"></span>
                    </div>
                </div>
                <div class="column"></div>
            </div>

            <div class="columns" style="margin-bottom:0px;">
                <div class="column"></div>
                <div class="column is-half">
                    
                </div>
                <div class="column"></div>
            </div>
            

            <div class="columns">
                <div class="column"></div>
                <div class="column is-half">
                    <h1 class="title is-centered">WhatsApp Chat Analyzer</h1>
                    <h2 class="subtitle">Upload your chat history and get some insights!</h2>
                </div>
                <div class="column"></div>
            </div>

            <br>
            <br>


            <!-- result -->
            <div id="result-form">
                <div class="columns">
                    <div class="column"></div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Chat Identifier</label>
                            <div class="control">
                            <input id="server-guid" class="input" type="text" name="guid" placeholder="">
                            </div>
                        </div>                        
                    </div>
                    <div class="column"></div>
                </div>

                <div class="columns">
                    <div class="column"></div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Chat Password</label>
                            <div class="control">
                            <input id="server-pass" class="input" type="text" name="pass" placeholder="">
                            </div>
                        </div>                        
                    </div>
                    <div class="column"></div>
                </div>

                <div class="columns">
                    <div class="column"></div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Chat Statistics URL</label>
                            <div class="control">
                            <a id="chat-stats-url" href="">-</a>
                            </div>
                        </div>                        
                    </div>
                    <div class="column"></div>
                </div>
            </div>


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
    </div>

    <div class="footer">
        <div class="container">
            <div class="columns">
                <div class="column"></div>
                <div class="column" style="text-align: center;">
                    &copy; <!-- 2019 bruness.org -->
                </div>
                <div class="column"></div>
            </div>
        </div>
    </div>

</div>


<script src="js/jq.js"></script>
<script src="js/main.js"></script>
</body>
</html>