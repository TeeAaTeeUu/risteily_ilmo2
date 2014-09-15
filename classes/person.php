<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_log', 'script_errors.log');
//ini_set('log_errors', 'On');

include_once 'xml_parser.php';
include_once 'form.php';
include_once 'validate.php';
include_once 'database.php';
include_once 'mailer.php';

class person extends validate {

    private $person;

    public function form($keys) {
        $this->form->createForm(xml_parser::PERSON, $keys);
    }

    public function validatePost($post) {
        $db = new database();
        $post["luokka"] = $db->get_group_luokka_from_db($post["group_id"]);

        $this->formatThings(xml_parser::GROUP, $post);
        $sizes = $this->sizes();

        $error = null;
        if ($sizes["luokka"]["values"][$post["luokka"]] <= $db->amount_of("person", "group_id", $post["group_id"])) {
            $error = $this->errorMessages["luokka"][$this::SIZE];
        }

        unset($post["luokka"]);

        $this->formatThings(xml_parser::PERSON, $post);

        $this->validateData();

        if (empty($this->errors) && $error == null)
            return true;
        if ($error != null)
            $this->errors[] = $error;
        return false;
    }

    public function newToDB() {
        $db = new database();


        $this->person = $db->put_person_to_db($this->keys());
    }

    public function listAndBill() {
        $this->sortPerson();
        
        $prices = $this->getPrice();
        
        $db = new database();
        $price = $this->calculatePrice($prices, $this->person, $db);
        $viite = $db->get_unique_viite();
        
        $db->update_db(array("price" => $price, "viite" => $viite), 'person', 'id', $this->person["id"]);

        unset($this->person["id"], $this->person["category_id"], $this->person["price"], $this->person["viite"]);
        
        $temp = '<table border="1">' . "\n";
        foreach ($this->person as $key => $value) {
            $temp .= $this->row($key, $value);
        }
        $temp .= '</table>';
        
        $temp2 = '<p>' . $this->xml->emailInfo() . "</p>\n\n<p>" . $temp . '</p>' . "\n\n" . '<p>Hinta risteilyllesi on ' . $price . '€ ja viitenumerosi on ' . $viite . '.</p>' . "\n\n" . '<p>' . $this->xml->payInfo() . "</p>" . "\n\n" . '<p><i>Järjestäjä A / Järjestäjä B<br />' . "\n" . 'email@testi.fi</i></p>';
        
        sendMail($this->person["sahkoposti"], "email@testi.fi", null, "email@testi.fi", "Testi Tapahtuma", $temp2);

        return $temp2;
    }

    private function row($key, $value) {
        return '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>' . "\n";
    }

    private function sortPerson() {
        $db = new database();
        $this->person["hytin nimi"] = $db->get_group_name_from_db($this->person["group_id"]);
    }

    public function getPrice() {
        $this->formatThings(xml_parser::GROUP, array());
        $prices = $this->prices;

        $this->formatThings(xml_parser::PERSON, array());
        return array_merge($prices, $this->prices);
    }

    public function calculatePrice($prices, $person, $db) {
        $price = 0;
        
        $luokka = $db->get_group_luokka_from_db($person["group_id"]);

        foreach ($prices as $key => $value) {
            foreach ($value as $bit) {
                if ($key == "liikennöinti" && $bit["name"] == $person["liikennointi"]) {
                    $price += $bit["value"];
                } elseif ($key == "luokka" && $bit["name"] == $luokka) {
                    $price += $bit["value"];
                } elseif (isset($person[$key]) && $person[$key] == "x") {
                    $price += $bit["value"];
                }
            }
        }
        return $price;
    }

}

?>