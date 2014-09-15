<?php

include_once 'xml_parser.php';
include_once 'validate.php';
include_once 'database.php';

class form {

    private $xml;
    private $what;

    const END = "\n";

    private $keys;

    public function __construct($xml) {
        $this->xml = $xml;
    }

    public function createForm($what, $keys) {
        $this->keys = $keys;
        $this->what = $what;

        echo '<form method="post">' . "\n";
        echo '<table>' . "\n";
        $this->formLoop($this->xml->informationBits($what));
        echo $this->row('<input type="submit" name="form" value="Lähetä">', "", "");
        echo '</table>' . "\n";
        echo '</form>' . "\n";
    }

    public function select($what, $array) {
        $temp = '<form method="post">' . "\n";
        $temp .= '<select name="' . $what . '_id">' . "\n";

        $array = $this->sortArray($array);

        foreach ($array as $key => $value) {
            $temp .= '<option value="' . $key . '">' . $value . '</option>' . "\n";
        }
        $temp .= '</select> ';
        $temp .= '<input type="submit" name="form" value="Valitse">' . "<br />\n";
        $temp .= '</form>' . "\n";

        return $temp;
    }

    private function sortArray($array) {
        $array2 = array();
        foreach ($array as $value) {
            $array2[$value["id"]] = $value["nimi"];
        }
        asort($array2);
        return $array2;
    }

    public function selectForms($what, $array) {
        if ($what == xml_parser::GROUP) {
            return $this->selectGroup($array);
        }
    }

    protected function selectGroup($groups) {
        $temp = "";
        if (!empty($groups)) {
            $temp .= '<table style="background-color:#33CCFF;">' . "\n";
            $first = true;
            foreach ($groups as $group) {
                if ($first)
                    $first = false;
                else
                    $temp .= '<tr style="background-color:#ADD8E6;height:2em;"><td></td><td></td></tr>' . "\n";
                $temp .= '<tr><td>' . wordwrap($group["nimi"], 22, '<br />' . "\n", true) . ' </td><td> ' . $group["luokka"] . '-luokka</td></tr>' . "\n";

                $temp2 = "";
                foreach ($group["persons"] as $person) {
                    $temp2 .= " - " . wordwrap($person, 30, '<br />' . "\n", true) . '<br />' . "\n";
                }

                $temp .= $this->row($temp2, wordwrap($group["lisatietoa"], 22, '<br />' . "\n", true));
                if ($group["select"])
                    $temp .= $this->row('<form method="post"><input type="hidden" name="nimi" value="' . $group["nimi"] . '"><input type="submit" name="select" value="valitse"></form>', "");
            }
            $temp .= '</table>' . "\n";
        }
        return $temp;
    }

    private function formLoop($information) {
        $db = new database();
        foreach ($information as $bit) {
            if ($this->xml->bitType($bit) == xml_parser::TEXT) {
                echo $this->textField($bit);
            } elseif ($this->xml->bitType($bit) == xml_parser::CHECK) {
                echo $this->checkField($bit);
            } elseif ($this->xml->bitType($bit) == xml_parser::CHOICE) {
                echo $this->choiceField($bit, $db);
            } elseif ($this->xml->bitType($bit) == xml_parser::GROUP) {
                echo $this->groupInfo($bit);
                $this->formLoop($bit->group->children());
            } elseif ($this->xml->bitType($bit) == xml_parser::TEXTAREA) {
                echo $this->textareaField($bit);
            }
        }
    }

    private function textField($bit) {
        $value = $this->getValueFromPost((string) $bit->name, "value");
        return $this->row($bit->name, '<input type="text" name="' . $bit->name . '"' . $value . '>', $this->info($bit));
    }

    private function textareaField($bit) {
        $value = $this->getValueFromPost((string) $bit->name, "textarea");
        return $this->row($bit->name, '<textarea name="' . $bit->name . '">' . $value . '</textarea>', $this->info($bit));
    }

    private function checkField($bit) {
        $value = $this->getValueFromPost((string) $bit->name, "checkbox");
        return $this->row($bit->name, '<input type="checkbox" name="' . $bit->name . '" value="x"' . $value . '>', $this->info($bit));
    }

    private function choiceField($bit, $db) {
        $choice = $this->getValueFromPost((string) $bit->name, "dropdown");
        $temp = '<select name="' . $bit->name . '">' . "\n";
        foreach ($bit->choice->children() as $value) {
            if (isset($value->amount) == false || (int) $value->amount > $db->amount_of($this->what, (string) $bit->name, (string) $value->name)) {
                $price = '';
                if (isset($value->price))
                    $price = ' (' . $value->price . $this->xml->currency() . ')';
                $choice2 = "";
                if ($choice[0] == (string) $value->name)
                    $choice2 = $choice[1];
                $temp .= '<option value="' . $value->name . '"' . $choice2 . '>' . $value->name . $price . '</option>' . "\n";
            }
        }
        $temp .= '</select>';

        return $this->row($bit->name, $temp, $this->info($bit));
    }

    private function groupInfo($bit) {
        return $this->row($bit->name, '<hr />', $this->info($bit));
    }

//assists methods

    private function info($bit) {
        if (isset($bit->info))
            return '(' . $bit->info . ')';
        elseif (isset($bit->check->price))
            return '(' . $bit->check->price . $this->xml->currency() . ')';
        return '';
    }

    private function row($a, $c, $d = null) {
        return '<tr><td>' . str_replace("_", " ", $a) . '</td><td>' . $c . '</td>' . ($d != null ? '<td>' . $d . '</td>' : "") . '</tr>' . $this::END;
    }

    public function getValueFromPost($name, $what) {
        if (isset($this->keys[$name]))
            if ($what == "value") {
                return ' value="' . $this->keys[$name] . '"';
            } elseif ($what == "textarea") {
                return $this->keys[$name];
            } elseif ($what == "checkbox") {
                return ' checked="checked"';
            } elseif ($what == "dropdown") {
                return array($this->keys[$name], ' selected="selected"');
            }
    }

}

?>
