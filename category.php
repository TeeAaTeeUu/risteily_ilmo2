<?php

include_once 'classes/category.php';
include_once 'classes/xml_parser.php';
include_once 'classes/form.php';
include_once 'classes/html.php';

$xml = new xml_parser();

if (time() >= $xml->startTime() && time() <= $xml->stopTime() || $xml->debug()) {

    $form = new form($xml);

    $category = new category($xml, $form);

//da shit starts here

    if (isset($_POST["form"])) {
//        if ($_POST["form"] == "Lähetä") {
//            if ($category->validatePost($_POST)) {
//                $category->newToDB();
//            }
//        } else
        if ($_POST["form"] == "Valitse" && isset($_POST["category_id"])) {
            header('Location: group.php?category_id=' . $_POST["category_id"], true, 303);
        }
    }

    top($xml->getSubject("category"));

    echo '<p>' . $xml->getInfo("category") . '</p><br />';

    $category->printCategorys();
    echo '<hr />';

    $category->printStats();
//    $count = 0;
//    foreach ($category->errors() as $error) {
//        echo " - " . $error . '<br />' . "\n";
//        $count++;
//    }
//    if ($count > 0)
//        echo '<hr />';
//
//    echo '<p>Haluatko luoda uuden ryhmän?<p>';
//    $category->form($category->keys());
} elseif (time() <= $xml->startTime()) {
    top();

    echo $xml->earlyMessage();
} else {
    top();
    echo $xml->lateMessage();
}

bottom();
?>