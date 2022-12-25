<?php
require(__DIR__ . '/defines.php');
use Lazer\Classes\Database as Lazer;

try{
    \Lazer\Classes\Helpers\Validate::table('pastebins')->exists();
} catch(\Lazer\Classes\LazerException $e){
    Lazer::create('pastebins', array(
        'id' => 'integer',
        'content' => 'string',
    ));
    echo "Table pastebins created, setup completed.\n";
}

function isSecure() {
    /* check if the connection is secture (https) */
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
}

function url() {
    /* return url of the current page */
    $protocol = isSecure()? "https": "http";
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
// check https
if (!isSecure()){echo "please use https\n"; exit; }

// authenticate
if (!isset($_SERVER['PHP_AUTH_USER']) ||
    !isset($_SERVER['PHP_AUTH_PW']) ||
    ($_SERVER['PHP_AUTH_USER'] !== USERNAME) ||
    ($_SERVER['PHP_AUTH_PW'] !== PASSWORD)) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo "You must login first\n";
    exit;
}

function menu() {
    /* returns a menu if not curl */
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if( preg_match('/curl[\/\s](\d+\.\d+)/', $agent) ) {
        header("Content-Type: text/plain");
    } else {
        header("Content-Type: text/html");
        echo "<title>BarraHome Pastebin</title>";
        echo "
            <a href='".$_SERVER['PHP_SELF']."'>Home</a> //  
            <a href='".$_SERVER['PHP_SELF']."?id=10'>Docs</a> //
            <a href='".$_SERVER['PHP_SELF']."?id=8'>CLI client</a> //
            <a href='".$_SERVER['PHP_SELF']."?id=11'>About</a>
        ";
        echo "<hr />";
    }
}

/* Requests, return html */
if (isset($_GET["id"])) {
    menu();
    $id  = intval($_GET["id"]);
    // GET
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (Lazer::table('pastebins')->where('id', '=', $id)->findAll()->count() != 1){
            header("HTTP/1.0 404 Not Found");
            echo "id " . $id . " does not exist\n";
        } else {
            $row = Lazer::table('pastebins')->find($id);
            echo "<pre>".$row->content."</pre>";
        }
    }
    
    // DELETE
    elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        menu();
        if (Lazer::table('pastebins')->where('id', '=', $id)->findAll()->count() != 1){
            header("HTTP/1.0 404 Not Found");
            echo "id " . $id . " does not exist\n";
        } else {
            Lazer::table('pastebins')->find($id)->delete();
            echo "id " . $id . " has been deleted\n";
        }
    }
    // UPDATE
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        menu();
        if (isset($_POST["c"])) {
            $content = $_POST["c"];
        } elseif (isset($_FILES["c"])) {
            $content = file_get_contents($_FILES['c']['tmp_name']);
        } else {
            echo "please provide a value for the field c\n";
            exit;
        }
        $row = Lazer::table('pastebins')->find($id);
        $row->content = $content; 
        $row->save();
        if (Lazer::table('pastebins')->where('id', '=', $id)->findAll()->count() != 1){
            echo "id " . $id . " did not change or does not exist\n";
        } else {
            echo "id " . $id . " has been updated\n";
        }
    }
    exit;
}

// INDEX
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    menu();
    $table = Lazer::table('pastebins')->findAll();
    echo "<ul>";
    foreach($table as $row) {
        $content = substr($row->content, 0, 100);
        echo "<li>
                <a href=".url() . "?id=" . $row->id.">" . $row->id."</a>
                <ul>
                    <li>".str_replace("\n", "\n\t", $content)."</li>
                </ul>
            </li>";
    }
    echo "</ul>";
    exit;
}

// POST
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    menu();
    if (isset($_POST["c"])) {
        $content = $_POST["c"];
    } elseif (isset($_FILES["c"])) {
        $content = file_get_contents($_FILES['c']['tmp_name']);
    } else {
        echo "please provide a value for the field c\n";
        exit;
    }
    $row = Lazer::table('pastebins');
    $row->content = $content;     
    $row->save();
    echo url() . "?id=" . $row->id . "\n";
    exit;
}
