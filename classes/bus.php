<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
ini_set('error_log', 'script_errors.log');
ini_set('log_errors', 'On');

include_once 'xml_parser.php';
include_once 'form.php';
include_once 'validate.php';
include_once 'database.php';
include_once 'mailer.php';

class bus extends validate {

    private $bus;

    public function form($keys) {
        $this->form->createForm(xml_parser::PERSON, $keys);
    }

    public function validatePost($post) {
        $this->formatThings(xml_parser::PERSON, $post);

        $this->validateData();

        if (empty($this->errors))
            return true;
        return false;
    }

    public function newToDB() {
        $db = new database();

        $this->bus = $db->put_person_to_db($this->keys());
    }

    public function listAndBill() {
        $prices = $this->getPrice();

        $db = new database();
        $price = $this->calculatePrice($prices, $this->bus);
        $viite = $db->get_unique_viite();

        $db->update_db(array("price" => $price, "viite" => $viite), 'person', 'id', $this->bus["id"]);

        unset($this->bus["id"], $this->bus["price"], $this->bus["viite"], $this->bus["syntymaaika"], $this->bus["kansallisuus"], $this->bus["etukortti"], $this->bus["aamiainen"], $this->bus["illallinen"], $this->bus["lounas"], $this->bus["group_id"]);

        $temp = '<table border="1">' . "\n";
        foreach ($this->bus as $key => $value) {
            $temp .= $this->row($key, $value);
        }
        $temp .= '</table>';

        $temp2 = '<p>' . $this->xml->emailInfo() . "</p>\n\n<p>" . $temp . '</p>' . "\n\n" . '<p>Hinta bussille on ' . $price . '€ ja viitenumerosi on ' . $viite . '.</p>' . "\n\n" . '<p>' . $this->xml->payInfo() . "</p>" . "\n\n" . '<p><i>Järjestäjä A / Järjestäjä B<br />' . "\n" . 'email@testi.fi</i></p>';

        sendMail($this->bus["sahkoposti"], "email@testi.fi", null, "email@testi.fi", "Testi Tapahtuma", $temp2);

        return $temp2;
    }

    private function row($key, $value) {
        return '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>' . "\n";
    }

    public function getPrice() {
        $this->formatThings(xml_parser::PERSON, array());
        return $this->prices;
    }

    public function calculatePrice($prices, $person) {
        $price = 0;

        foreach ($prices as $key => $value) {
            foreach ($value as $bit) {
                if ($key == "liikennöinti" && $bit["name"] == $person["liikennointi"]) {
                    $price += $bit["value"];
                }
            }
        }
        return $price;
    }

    public function listPeople() {
        $db = new database();
        $persons = $db->get_persons_names_by_group_from_db(0);

        $temp = "<h2>Ilmoittautuneet</h2>\n";
        $temp .= "<p>\n";

        foreach ($persons as $person) {
            $temp .= $person . "<br/>\n";
        }
        $temp .= "</p>\n";

        if (count($persons) > 0)
            $temp .= "<p>Ilmoittautuneita on " . count($persons) . " kpl.</p>\n";
        else
            $temp .= "<p>Ilmoittautuneita on " . count($persons) . " kpl.</p>\n";
        return $temp;
    }

}

?>