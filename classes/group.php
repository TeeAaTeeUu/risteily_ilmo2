<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_log', 'script_errors.log');
//ini_set('log_errors', 'On');

include_once 'xml_parser.php';
include_once 'form.php';
include_once 'validate.php';
include_once 'database.php';

class group extends validate {

    private $post;

    public function form($keys) {
        $this->form->createForm(xml_parser::GROUP, $keys);
    }

    public function validatePost($post) {
        $this->formatThings(xml_parser::GROUP, $post);

        $this->validateData();

        if (empty($this->errors))
            return true;
        return false;
    }

    public function printGroups($category_id) {
        $db = new database();
        $groups = $db->get_groups_from_db($category_id);

        if (empty($groups)) {
            echo '<p>Kukaan ei ole vielä ilmoittautunut tähän kategoriaan.</p>';
        } else {
            for ($i = 0; $i < count($groups); $i++) {
                $groups[$i]["persons"] = $db->get_persons_names_by_group_from_db($groups[$i]["id"]);
            }

            $this->formatThings(xml_parser::GROUP, array());
            $sizes = $this->sizes();

            foreach ($groups as $key => $group) {
                if ($sizes["luokka"]["values"][$group["luokka"]] > $db->amount_of("person", "group_id", $group["id"])) {
                    $groups[$key]["select"] = true;
                } else {
                    $groups[$key]["select"] = false;
                }
            }

            usort($groups, "cmp");

            echo $this->form->selectForms($this::GROUP, $groups);
        }
    }

    public function newToDB() {
        $db = new database();
        $db->put_group_to_db($this->keys());
        return $db->get_group_id_from_db($this->keys["nimi"]);
    }

    public function validateSelect($post, $group_id) {
        $this->formatThings(xml_parser::GROUP, $post);

        $db = new database();

        if ($db->exists("group", "id", $group_id) == false)
            return false;

        $sizes = $this->sizes();

        if ($sizes["luokka"]["values"][$db->get_group_luokka_from_db($group_id)] > $db->amount_of("person", "group_id", $group_id)) {
            return true;
        } else {
            $this->errors[] = $this->errorMessages["luokka"][$this::SIZE];
            return false;
        }
    }

    public function groupID($name) {
        $db = new database();
        return $db->get_group_id_from_db($name);
    }

}

function cmp($a, $b) {
    if ($a["select"] && $b["select"] || !$a["select"] && !$b["select"]) {
        return strnatcmp($a["nimi"], $b["nimi"]);
    } elseif ($a["select"]) {
        return -1;
    } else {
        return 1;
    }
}

?>