<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

include_once '../classes/validate.php';
include_once '../classes/xml_parser.php';
include_once '../classes/database.php';
include_once '../classes/helper.php';

class install extends validate {

    private $person;
    private $group;
    private $category;
    private $tables = array("person", "group", "category");

    public function setup($db) {
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');

        foreach ($this->tables as $what) {
            $this->format($what);
        }

        $mysql = $db->get_mysql();

        echo '<pre>';
        echo '<h2>luontikäskyt</h2>';

        mysqli_query($mysql->connection(), "SET NAMES 'utf8'") or die(mysqli_error($mysql->connection()));

        foreach ($this->tables as $value) {
            $table = $mysql->etuliite . $value;

            $query = "CREATE TABLE IF NOT EXISTS " . "\n" . $table . "( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (id)" . "\n";

            foreach ($this->{$value} as $key => $value) {
                $query .= ", ";
                if (strpos($key, '_id') !== false) {
                    $query .= clean($key) . " INT" . "\n";
                }
                else
                    $query .= clean($key) . " VARCHAR(". $value . ")" . "\n";
            }

            $query .= ", aika timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP" . "\n";
            $query .= " )";

            echo $query . "\n" . "\n";

            mysqli_query($mysql->connection(), $query) or die(mysqli_error($mysql->connection()));
        }
        echo '</pre>';
    }

    //helping methods

    private function format($what) {
        $this->formatThings($what, array());
        $maxs = $this->maxs();

        $temp = array();
        foreach ($this->keys() as $key => $value) {
            if (strpos($key, '_id') !== false) {
                
            } else {
                $temp[$key] = $maxs[$key];
            }
        }

        if ($what == "group") {
            $temp["category_id"] = 1;
        }
        if ($what == "person") {
            $temp["group_id"] = 1;
            $temp["price"] = 5;
            $temp["viite"] = 10;
        }

        $this->$what = $temp;
    }

    public function put_category_autofill_to_db($db, $cat) {
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
        
        echo '<pre>';
//        $old_categorys = $db->get_categorys_from_db();
//        
//        echo '<h2>vanhat</h2>';
//        
//        var_dump($old_categorys);
//        
//        echo '<h2>settareissa</h2>';
//
//        var_dump($xml_categorys);
//
//        $new_categorys = array_diff($xml_categorys, $old_categorys);

        echo '<h2>lisätään</h2>';

        $xml_categorys = $cat->autofillCategorys();

        var_dump($xml_categorys);

        foreach ($xml_categorys as $category) {
            $db->put_category_to_db($category);
            echo "\n";
        }
        echo '</pre>';
    }

}

?>
