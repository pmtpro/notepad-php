<?php

// Base URL. Example: http://google.com
define('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME']);

// Config Database
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = 'root';
const DB_NAME = 'notepad';

// Disable cache
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// class
require __DIR__ . '/Note.class.php';

// var
$note  = $_GET['note'] ?? '';
$text  = $_POST['text'] ?? '';
$raw   = isset($_GET['raw']);
$_note = new Note($note);

if ($_note->id) {
    // Note exists

    // get raw
    if ($raw) {
        header('Content-type: text/plain');
        echo $_note->data->text;
        exit;
    }

    // update note
    if (!empty($text)) {
        $_note->update($text);
        exit;
    }
} else {
    if (empty($note)) {
        // new note
        $_note->new();
    } else {
        // note not fount
        exit('Not found');
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($note) ?></title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #D0D3D4;
        }

        .container {
            position: absolute;
            top: 20px;
            right: 20px;
            bottom: 20px;
            left: 20px;
        }

        #content {
            font-size: 100%;
            margin: 0;
            padding: 20px;
            overflow-y: auto;
            resize: none;
            width: 100%;
            height: 100%;
            min-height: 100%;
            box-sizing: border-box;
            border: 0;
            outline: none;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: #17202A;
            }

            #content {
                background: rgb(52, 61, 70);
                color: rgb(216, 222, 233);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <label for="content"></label>
    <textarea id="content"><?= htmlspecialchars($_note->data->text, ENT_QUOTES) ?></textarea>
</div>

<script type="text/javascript">
    function syncContent() {
        var textarea = document.getElementById('content');
        var content  = textarea.value;
        var request  = new XMLHttpRequest();

        request.open('POST', window.location.href);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.send('text=' + encodeURIComponent(content));
    }

    // sync 1s
    setInterval(syncContent, 1000)
</script>
</body>
</html>
