<?php

include_once 'classes/person.php';
include_once 'classes/xml_parser.php';
include_once 'classes/form.php';
include_once 'classes/html.php';
include_once 'classes/database.php';

$xml = new xml_parser();

if (time() >= $xml->startTime() && time() <= $xml->stopTime() || $xml->debug()) {

    $form = new form($xml);

    $person = new person($xml, $form);

    $kiitos = null;

//da shit starts here

    if (isset($_POST["form"]) && isset($_GET["group_id"])) {
        $_POST["group_id"] = $_GET["group_id"];
        if ($person->validatePost($_POST)) {
            $person->newToDB();
            $kiitos = $person->listAndBill();
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
        foreach ($person->errors() as $error) {
            echo " - " . $error . '<br />' . "\n";
            $count++;
        }
        if ($count > 0)
            echo '<hr />';

        $person->form($person->keys());
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