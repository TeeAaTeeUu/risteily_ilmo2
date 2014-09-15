<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
ini_set('error_log', 'script_errors.log');
ini_set('log_errors', 'On');

include_once 'classes/bus.php';
include_once 'classes/xml_parser.php';
include_once 'classes/form.php';
include_once 'classes/html.php';
include_once 'classes/database.php';

$xml = new xml_parser(getcwd() . "/bus.xml");

if (time() >= $xml->startTime() && time() <= $xml->stopTime() || $xml->debug()) {

    $form = new form($xml);

    $bus = new bus($xml, $form);

    $kiitos = null;

//da shit starts here

    if (isset($_POST["form"])) {
        if ($bus->validatePost($_POST)) {
            $bus->newToDB();
            $kiitos = $bus->listAndBill();
        }
    }

    if ($kiitos == null) {
        top($xml->getSubject("person"));
    } else {
        top("kiitos!");
    }

    if ($kiitos == null) {
        echo '<p>' . $xml->getInfo("person") . '</p><br />';

        $count = 0;
        foreach ($bus->errors() as $error) {
            echo " - " . $error . '<br />' . "\n";
            $count++;
        }
        if ($count > 0)
            echo '<hr />';

        $bus->form($bus->keys());
        
        echo '<hr />';
        
        echo $bus->listPeople();
        
    } else {
        echo '<p>' . $kiitos . '</p><br />';
    }
} elseif (time() <= $xml->startTime()) {
    top();

    echo $xml->earlyMessage();
} else {
    top();
    echo $xml->lateMessage();
}
bottom();
?>