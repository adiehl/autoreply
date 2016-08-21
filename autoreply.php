#!/usr/bin/php
<?php
/**
 * Simple Vacation Autoresponder-Script
 * @author Andreas Diehl <andreas.diehl@iwr.uni-heidelberg.de>
 * @license GPL
 */

// read the settings
$settings = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'settings.json'));
// read the reply message
$message = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'message.txt');
// read the email
$data = stream_get_contents(STDIN);
try {
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'database.json')) {
        $emailDb = unserialize(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'database.json'));
    } else {
        $emailDb = [];
    }
} catch (Exception $e) {
    $emailDb = [];
}

preg_match('/From ([A-Za-z0-9.-]+@[A-Za-z0-9.-]+)  /', $data, $matches);
$recipient = $matches[1];

// ensure email is only sent once
if (!isset($emailDb[$recipient])) {
    $subject = $settings->subject;
    $header = 'From: ' . $settings->sender . "\r\n" .
        'X-Mailer: Vacation';

    mail($recipient, $subject, $message, $header);
    $emailDb[$recipient] = true;

    // save database
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'database.json', serialize($emailDb));
}

