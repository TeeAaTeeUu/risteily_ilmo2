<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_log', 'script_errors.log');
//ini_set('log_errors', 'On');

include_once 'classes/group.php';
include_once 'classes/xml_parser.php';
include_once 'classes/form.php';
include_once 'classes/html.php';

$xml = new xml_parser();

if (time() >= $xml->startTime() && time() <= $xml->stopTime() || $xml->debug()) {

    $form = new form($xml);

    $group = new group($xml, $form);

//da shit starts here

    if (isset($_POST) && isset($_GET["category_id"])) {
        $_POST["category_id"] = $_GET["category_id"];
        if (isset($_POST["form"])) {
            if ($group->validatePost($_POST)) {
                $id = $group->newToDB();
                nextFile($id);
            }
        } elseif (isset($_POST["select"])) {
            $group_id = $group->groupID($_POST["nimi"]);
            if ($group->validateSelect($_POST, $group_id)) {
                nextFile($group_id);
            }
        }
    }
    top($xml->getSubject("group"));

    echo '<p>' . $xml->getInfo("group") . '</p><br />';
    
    $count = 0;
    foreach ($group->errors() as $error) {
        echo " - " . $error . '<br />' . "\n";
        $count++;
    }
    if ($count > 0)
        echo '<hr />';

    $keys = $group->keys();

    if (isset($_GET["category_id"])) {
        $keys["category"] = $_GET["category_id"];
        $group->form($keys);

        echo '<hr />' . "\n";
        $group->printGroups($_GET["category_id"]);
    } else {
        echo "what is this madness?!?";
    }
} elseif (time() <= $xml->startTime()) {
    top();

    echo $xml->earlyMessage();
} else {
    top();
    echo $xml->lateMessage();
}

bottom();

function nextFile($id) {
    header('Location: person.php?group_id=' . $id, true, 303);
}
?>