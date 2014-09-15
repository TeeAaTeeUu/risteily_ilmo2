<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_log', 'script_errors.log');
//ini_set('log_errors', 'On');

include_once 'xml_parser.php';
include_once 'form.php';
include_once 'validate.php';
include_once 'database.php';

class category extends validate {

    protected $db;
    protected $categorys;

    public function form($keys) {
        $this->form->createForm(xml_parser::CATEGORY, $keys);
    }

    public function validatePost($post) {
        $this->formatThings(xml_parser::CATEGORY, $post);
        
        $this->validateData();

        if (empty($this->errors))
            return true;
        return false;
    }
    
    public function autofillCategorys() {
        $this->formatThings(xml_parser::CATEGORY, array());
        return $this->autofill[$this::CATEGORY];
    }
    
    public function printCategorys() {
        $this->db = new database();
        $this->categorys = $this->db->get_categorys_from_db();
        
        echo $this->form->select($this::CATEGORY, $this->categorys);
    }
    
    public function newToDB() {
        $db = new database();
        $db->put_category_to_db($this->keys());
    }
    
    public function printStats() {
        $temp = array();
        foreach ($this->categorys as $category) {
            $temp[] = $category["id"];
        }
        
        $groups = $this->db->get_groups_from_db_by_category_ids($temp);
        
        echo '<p>' . "\n";
        foreach ($this->categorys as $category) {
            echo $category["nimi"] . " : " . $this->db->get_person_counts_from_db($groups[$category["id"]]) . " hlö " . ($groups[$category["id"]][0] == -1 ? 0 : count($groups[$category["id"]])) . " hytissä.<br />\n";
        }
        echo '</p>' . "\n";
    }

}

?>