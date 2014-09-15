<?php

include_once 'install.php';
include_once '../classes/xml_parser.php';
include_once '../classes/form.php';
include_once '../classes/database.php';
include_once '../classes/category.php';

$xml = new xml_parser("../settings.xml");
$install = new install($xml, new form($xml));
$db = new database();
$form = new form($xml);
$cat = new category($xml, $form);

$install->setup($db);

$install->put_category_autofill_to_db($db, $cat);

?>
