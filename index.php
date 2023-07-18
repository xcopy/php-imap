<?php

require 'vendor/autoload.php';

use Webklex\PHPIMAP\{ClientManager, Folder, Message};

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$cm = new ClientManager();

$client = $cm->make([
    'host' => $_ENV['HOST'],
    'port' => $_ENV['PORT'],
    'username' => $_ENV['USERNAME'],
    'password' => $_ENV['PASSWORD'],
    'protocol' => 'imap',
    'encryption' => 'ssl',
]);

$client->connect();
// dd($client->isConnected(), $client->checkConnection());

$folders = $client->getFolders($hierarchical = true);

/** @var Folder $folder */
/** @var Folder $f */
foreach ($folders as $folder) {
    folder($folder);

    if ($folder->hasChildren()) {
        foreach ($folder->children as $f) {
            folder($f);
        }
    }
}

function folder (Folder $f) {
    echo "- $f->name\n";
    // echo '-- ' . json_encode($f->examine()) . "\n";
    // $overview = $f->overview($sequence = "1:*");
    // $overview and dd($overview);

    // $query = $f->query();
    $messages = $f->query()->all()->get();

    /** @var Message $message */
    foreach ($messages as $message) {
        echo "--- Subject: $message->subject\n";
        echo "--- From: $message->from\n\n";
    }
};
