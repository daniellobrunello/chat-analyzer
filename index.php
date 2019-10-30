<?php
// header stuff?
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">

    <link rel="shortcut icon" type="image/png" href="favicon.png"/>

    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/reset.css">
    <!-- <link rel="stylesheet" href="css/bulma.css"> -->
    <link rel="stylesheet" href="css/bulma-custom.css">
    <link rel="stylesheet" href="css/style.css">
    
    <title>Chat Analyser | WhatsApp Group Chat Analyser</title>
</head>
<body>

<div id="option-1-howto" class="modal is-clipped">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">How to upload a chat file</p>
            <button class="delete close-option-1-howto" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="content">
                <p class="title">1. Select the 3 dots in your chat</p>
                <figure class="image has-ratio">
                    <img src="images/step1.jpg">
                </figure>
            </div>
            
            <div class="content">
                <p class="title">2. Tap on 'more'</p>
                <figure class="image has-ratio">
                    <img src="images/step2.jpg">
                </figure>
            </div>
            
            <div class="content">
                <p class="title">3. Select 'export chat'</p>
                <figure class="image has-ratio">
                    <img src="images/step3.jpg">
                </figure>
            </div>
            <div class="content">
                <p class="title">4. Choose 'export without attachments'</p>
            </div>
            
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success close-option-1-howto">OK, cool!</button>
        </footer>
    </div>
</div>

<div class="hero is-primary is-bold">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-1">WhatsApp Chat Analyser</h1>
            <h2 class="subtitle is-3">Upload your chat history and get some interesting insights!</h2>
            <h3 class="subtitle is-4">
                <span class="title-feature">🗣 Who talks most?</span>
                <span class="title-feature">🙊 Who is the quiet one?</span>
                <span class="title-feature">🖊 Most common words</span>
                <span class="title-feature">🕓 Activity times</span>
            <h3>
        </div>
    </div>
</div>
<div class="section">
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
        

        <div class="columns">
            <div class="column">
                <div class="box">
                    <p class="title is-5">Option 1 - Upload</p>
                    <p class="subtitle">Upload your chat file directly</p>
                    <div class="content">
                        <p>Export the chat converation history of one of your (group) chats and upload the resulting TXT-file here.</p>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="buttons has-addons is-centered">
                                <a href="#upload-txt"><button class="button is-primary">Start</button></a>
                            </div>
                        </div>
                        <div class="column">
                            <div class="buttons has-addons is-centered">
                                <button id="option-1-howto-btn" class="button">How To</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="box">
                    <p class="title is-5">Option 2 - Email</p>
                    <p class="subtitle">Send your chat file via Email (in development)</p>
                    <div class="content">
                        <p>Export the chat converation history of one of your (group-) chats and send the resulting .txt-file via Email to us.
                            You will receive a link to the results via Email to the address you sent the chat from.<br>
                        </p>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="buttons has-addons is-centered">
                                <button class="button" disabled>This is not possible, yet!</button>
                            </div>
                        </div>
                        <div class="column">
                            <div class="buttons has-addons is-centered">
                                <button id="option-2-howto-btn" class="button" disabled>How To</button>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>

        <div class="tabs is-centered">
            <div id="upload-txt"></div>
            <ul>
                <li class="is-active">
                    <a>
                        <span class="icon is-small"><i class="fas fa-file-upload" aria-hidden="true"></i></span>
                        <span>Upload</span>
                    </a>
                </li>
                <li>
                    <a>
                        <span class="icon is-small"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                        <span>Email</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- result -->
        <div id="result-form">

            <article class="message is-dark">
                <div class="message-header">
                    <p>Credentials</p>
                    <button class="delete" aria-label="delete"></button>
                </div>
                <div class="message-body">
                    <div class="columns">
                        <div class="column"></div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label">Chat Identifier</label>
                                <div class="control">
                                <input id="server-guid" class="input is-primary" type="text" name="guid" placeholder="" readonly>
                                </div>
                            </div>                        
                        </div>
                        <div class="column"></div>
                    </div>

                    <div class="columns">
                        <div class="column"></div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label">Chat Password</label>
                                <div class="control">
                                <input id="server-pass" class="input is-primary" type="text" name="pass" placeholder="" readonly>
                                </div>
                            </div>                        
                        </div>
                        <div class="column"></div>
                    </div>

                    <div class="columns">
                        <div class="column"></div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label">Chat Statistics URL</label>
                                <div class="control">
                                    <textarea id="chat-stats-url" class="textarea is-primary" placeholder="-" readonly></textarea>
                                </div>
                            </div>       
                            <div class="buttons has-addons is-centered">
                                <a id="chat-stats-url-btn" href=""><button class="button">Go there!</button></a>
                            </div>                 
                        </div>
                        <div class="column"></div>
                    </div>
                </div>
            </article>
        </div>


        <form id="upload-form" action="" method="POST" enctype="multipart/form-data">
            
            <div class="columns">
                <div class="column"></div>
                <div class="column is-half">
                <article class="message is-dark">
                    <div class="message-body">
                    <strong>You worry about data privacy?</strong><br>That's good, we do so, too!
                    We ensure that your chat history will be 100% private and that nobody can read or access your files. 
                    Your statistics belong to you and to the people you share the link with. <a href="#faq">Read the FAQ!</a>
                    </div>
                    </article>
                </div>
                <div class="column"></div>
            </div>


            <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
            <div class="columns">
                <div class="column"></div>
                <div class="column">
                    <div class="field">
                        <div class="file is-centered is-boxed is-primary has-name">
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
                            <span id="filename" class="file-name">...</span>
                            </label>
                        </div>
                    </div>

                    <div id="name-field" class="field hidden">
                        <label class="label">Give it a name: <span class="tag is-info">Info</span></label>
                        
                        <div class="control">
                            <input id="chat-name" class="input is-primary" type="text" name="chatname" max-length="32" placeholder="Group name">
                        </div>
                    </div>
                </div>
                <div class="column"></div>
            </div>

            <br>

            <div id="upload-btn-columns" class="columns hidden">
                <div class="column"></div>
                <div class="column">
                    <a id="upload-btn" class="button is-primary is-fullwidth is-large" disabled>Upload</a>
                    <div id="progress-bar">
                        <progress class="progress is-primary" value="15" max="100"></progress>
                    </div>
                </div>
                <div class="column"></div>
            </div>
        </form>
    </div>
</div>

<div id="faq" class="section">
    <div class="container">
        <h2 class="title is-2">FAQ</h2>
        <div class="content">
            <ol class="is-upper-roman">
                <h4 class="title is-4">What happens with my uploaded files?</h4>
                <blockquote>
                    After your file has been uploaded, it's getting renamed with a random file name and processed (e.g. words are getting count).
                    The results are stored encrypted in a secure database and your chat file will be deleted immediatly. Nobody can read the chat conversation.
                </blockquote>
                <br>
                <h4 class="title is-4">Who can see my statistics?</h4>
                <blockquote>
                    Only people with the link or your chat ID <strong>and</strong> the chat password can see your statistics. That means if you loose your chat password
                    or chat ID, you will not be able to see your statistics again. We're not able to recover the key for you.
                </blockquote>
                <br>
                <h4 class="title is-4">Is my content encrypted?</h4>
                <blockquote>
                    Yes! The file upload is TLS (also know as SSL) encrypted and your processed results are stored AES (Advanced Encryption Standard) encrypted in a secure database.
                    Nobody without your chat password can see any of your data.
                </blockquote>
                <br>
                <h4 class="title is-4">Can I upload Telegram/Signal/Threema chat conversation logs?</h4>
                <blockquote>
                    Sorry, at the moment, only WhatsApp conversations are supported. We are working on it to provide these functionalities in the future.
                    Anyway, it might be working if the chat log has the same or similiar structure as a WhatsApp chat log.
                </blockquote>
            </ol>
        </div>
    </div>
</section>

<br><br><br>

<div class="footer">
    <div class="container">
        <div class="columns">
            <div class="column"></div>
            <div class="column" style="text-align: center;">
                &copy; 2019 CoolestCompanyEver Inc. <!-- 2019 bruness.org -->
            </div>
            <div class="column"></div>
        </div>
    </div>
</div>



<script src="js/jq.js"></script>
<script src="js/main.js"></script>
</body>
</html>