<?php

include_once 'database.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

class validate {

    protected $keys;
    protected $maxs;
    protected $mins;
    protected $musthaves;
    protected $groups;
    protected $xml;
    protected $form;
    protected $errors;
    protected $errorMessages;
    protected $mustcontain;
    protected $duplicates;
    protected $autofill;
    protected $what;
    protected $sizes;
    protected $amounts;
    protected $prices;

    const MIN = "min";
    const MAX = "max";
    const ANY = "any";
    const NAME = "name";
    const BITS = "bits";
    const MUSTHAVE = "musthave";
    const MUSTCONTAIN = "mustcontain";
    const CONDITION = "condition";
    const IF2 = "if";
    const THEN = "then";
    const PARENT = "parent";
    const NO =
    "no";
    const DUPLICATE =
    "duplicates";
    const AMOUNT =
    "amount";
    const SIZE =
    "size";

    const CATEGORY =
    "category";
    const GROUP =
    "group";
    const PERSON =
    "person";

    public function __construct(

    $xml, $form) {
    $this-> xml = $xml;
    $this->form = $form;
}

public function keys() {
    return $this->keys;
}

public function maxs() {
    return $this->maxs;
}

public function sizes() {
    return $this->sizes;
}

public function amounts() {
    return $this->amounts;
}

public function errors() {
    if (empty($this->keys))
        return array();
    return $this->errors;
}

protected function loopInformation($what, $information) {
    foreach ($information as $bit) {
        $bit->name = str_replace(" ", "_", (string) $bit->name);
        if ($this->xml->bitType($bit) == xml_parser::GROUP) {
            $this->setGroupCondition($bit);
            $this->loopInformation($what, $bit->group->children());
        } else {
            $this->keys[(string) $bit->name] = null;
            $this->setMinAndMaxAndMusthaveAndContains($bit);
        }
        $this->setErrorMessages($bit);
    }
    if ($what == $this::GROUP)
        $this->keys["category_id"] = null;
    elseif ($what == $this::PERSON)
        $this->keys["group_id"] = null;
}

protected function loopAutofill($what, $rows) {
    $temp = array();
    foreach ($rows as $row) {
        $temp2 = array();
        foreach ($row->children() as $bit) {
            $temp2[(string) $bit->name] = utf8_encode((string) $bit->content);
        }
        $temp[] = $temp2;
    }
    $this->autofill[$what] = $temp;
}

protected function loopPost($post) {
    foreach ($this->keys as $key => $value) {
        $key = str_replace(" ", "_", $key);
        if (isset($post[$key]))
            $this->keys[$key] = $post[$key];
    }
}

public function formatThings($what, $post) {
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    mb_http_input('UTF-8');

    $this->keys = array();
    $this->maxs = array();
    $this->mins = array();
    $this->musthaves = array();
    $this->mustcontain = array();
    $this->errors = array();
    $this->groups = array();
    $this->duplicates = array();
    $this->autofill = array();
    $this->sizes = array();
    $this->amounts = array();
    $this->prices = array();

    $this->what = $what;

    $this->loopInformation($what, $this->xml->informationBits($what));
    $this->loopAutofill($what, $this->xml->autofillRows($what));
    $this->loopPost($post);
}

protected function setMinAndMaxAndMusthaveAndContains($bit) {
    $this->mins[(string) $bit->name] = (int) $bit->length->min;
    $this->maxs[(string) $bit->name] = (int) $bit->length->max;

    if ($bit->musthave == xml_parser::YES)
        $this->musthaves[(string) $bit->name] = true;
    else
        $this->musthaves[(string) $bit->name] = false;

    $this->setContains($bit);
    $this->setDuplicates($bit);
    $this->setAmountsAndSizes($bit);
    $this->setPrices($bit);
}

protected function setAmountsAndSizes($bit) {
    if (isset($bit->choice)) {
        $this->sizes[(string) $bit->name]["name"] = (string) $bit->name;
        $this->amounts[(string) $bit->name]["name"] = (string) $bit->name;

        foreach ($bit->choice->children() as $value) {
            if (isset($value->size))
                $this->sizes[(string) $bit->name]["values"][(string) $value->name] = (int) $value->size;
            if (isset($value->amount))
                $this->amounts[(string) $bit->name]["values"][(string) $value->name] = (int) $value->amount;
        }
    }
}

protected function setDuplicates($bit) {
    if ($bit->duplicates == $this::NO) {
        $this->duplicates[(string) $bit->name] = "no";
    } else {
        $this->duplicates[(string) $bit->name] = "";
    }
}

protected function setContains($bit) {
    if (isset($bit->mustcontain)) {
        $temp = array();
        foreach ($bit->mustcontain->children() as $value) {
            if ($value->getName() == $this::ANY)
                foreach ($value as $value2) {
                    $temp[] = (string) $value2;
                }
        }
        $this->mustcontain[(string) $bit->name] = $temp;
    }
}

protected function setGroupCondition($bit) {
    $temp = array();

    $temp[$this::MIN] = (int) $bit->musthave->min;
    $temp[$this::MAX] = (int) $bit->musthave->max;
    $temp[$this::NAME] = (string) $bit->name;

    $temp[$this::BITS] = $this->setGroupConditionBits($bit);
    $temp[$this::CONDITION] = $this->setGroupTest($bit);

    $this->groups[] = $temp;
}

private function setGroupConditionBits($bit) {
    $temp = array();
    foreach ($bit->group->children() as $bit2) {
        $temp[] = (string) $bit2->name;
    }
    return $temp;
}

private function setGroupTest($bit) {
    if (isset($bit->conditions->condition)) {
        $temp2 = array();
        foreach ($bit->conditions->children() as $condition) {
            if ($condition->getName() == $this::CONDITION) {
                $temp2[] = $this->SetGroupTestCondition($condition);
            }
        }
        return $temp2;
    }
    return array();
}

private function SetGroupTestCondition($condition) {
    $temp = array($this::IF2 => array(), $this::THEN => array(), $this::NAME => (string) $condition->name);

    foreach ($condition->children() as $value) {
        if ($value->getName() == $this::IF2)
            $temp[$this::IF2][] = array($this::PARENT => (string) $value->parent, $this::NAME => (string) $value->name);
        elseif ($value->getName() == $this::THEN)
            $temp[$this::THEN][] = array($this::PARENT => (string) $value->parent, $this::NAME => (string) $value->name);
    }
    return $temp;
}

protected function validateData() {
    $this->validateLengths();
    $this->validateContains();
    $this->validateGroups();
    $db = new database();
    $this->validateDuplicates($db);
    $this->validateAmounts($db);
    $this->validateSizes($db);
}

private function setErrorMessages($bit) {
    if (isset($bit->errors)) {
        $temp = array();
        foreach ($bit->errors->children() as $error) {
            $temp[(string) $error->name] = (string) $error->message;
        }

        $this->errorMessages[(string) $bit->name] = $temp;
    }
}

private function validateLengths() {
    foreach ($this->keys as $key => $value) {

        if (strpos($key, '_id') !== false) {
            
        } else {
            if ($this->musthaves[$key] || !empty($value)) {
                if ($this->validateMusthave($key, $value)) {
                    $this->validateMax($key, $value);
                    $this->validateMin($key, $value);
                }
            }
        }
    }
}

private function validateMax($key, $value) {
    if ($this->maxs[$key] != 0)
        if (strlen($value) > $this->maxs[$key])
            $this->errors[] = $this->errorMessages[$key][$this::MAX];
}

private function validateMin($key, $value) {
    if ($this->mins[$key] != 0)
        if (strlen($value) < $this->mins[$key])
            $this->errors[] = $this->errorMessages[$key][$this::MIN];
}

private function validateMusthave($key, $value) {
    if ($this->musthaves[$key] && empty($value)) {
        $this->errors[] = $this->errorMessages[$key][$this::MUSTHAVE];
        return false;
    }
    return true;
}

private function validateGroups() {
    foreach ($this->groups as $group) {
        $this->validateGroupLimits($group);
        $this->validateGroupConditions($group);
    }
}

private function validateGroupLimits($group) {
    if ($group[$this::MAX] != 0 || $group[$this::MIN] != 0) {
        $count = 0;
        foreach ($group[$this::BITS] as $bit) {
            if (isset($this->keys[$bit]))
                $count++;
        }
        if ($group[$this::MAX] != 0 && $count > $group[$this::MAX] || $group[$this::MIN] != 0 && $count < $group[$this::MIN]) {
            $this->errors[] = $this->errorMessages[$group[$this::NAME]][$this::MUSTHAVE];
        }
    }
}

private function validateGroupConditions($group) {
    if (!empty($group[$this::CONDITION])) {
        foreach ($group[$this::CONDITION] as $condition) {
            if (!$this->validateGroupConditionsLoop($condition)) {
                $this->errors[] = $this->errorMessages[$group[$this::NAME]][$condition[$this::NAME]];
            }
        }
    }
}

private function validateGroupConditionsLoop($condition) {
    foreach ($condition[$this::IF2] as $if) {
        if ($this->keys[$if[$this::PARENT]] == $if[$this::NAME]) {
            if ($this->validateGroupConditionsLoopThen($condition) >= count($condition[$this::THEN]))
                return false;
        }
    }
    return true;
}

private function validateGroupConditionsLoopThen($condition) {
    $count = 0;
    foreach ($condition[$this::THEN] as $then) {
        if ($then[$this::NAME] != $this->keys[$then[$this::PARENT]]) {
            $count++;
        }
    }
    return $count;
}

private function validateContains() {
    foreach ($this->mustcontain as $key => $list) {
        $count = 0;
        foreach ($list as $value) {
            if (strlen(strstr($this->keys[$key], $value)) <= 0)
                $count++;
        }
        if ($count >= count($list))
            $this->errors[] = $this->errorMessages[$key][$this::MUSTCONTAIN];
    }
}

public function validateDuplicates($db) {
    foreach ($this->duplicates as $key => $value) {
        if ($value == "no")
            if ($db->exists($this->what, $key, $this->keys[$key]))
                $this->errors[] = $this->errorMessages[$key][$this::DUPLICATE];
    }
}

public function validateAmounts($db) {
    $ok = true;
    foreach ($this->amounts as $bit) {
        if (isset($bit["values"]) && $this->keys[$bit["name"]] != null) {
            if ($bit["values"][$this->keys[$bit["name"]]] <= $db->amount_of($this->what, $bit["name"], $this->keys[$bit["name"]])) {
                $this->errors[] = $this->errorMessages[$bit["name"]][$this::AMOUNT];
                $ok = false;
            }
        }
    }
    return $ok;
}

public function validateSizes($db) {
    $ok = true;
    foreach ($this->sizes as $bit) {
        if (isset($bit["values"])  && $this->keys[$bit["name"]] != null && isset($this->keys["group_id"])) {
            if ($bit["values"][$this->keys[$bit["name"]]] <= $db->amount_of($this->what, array($bit["name"], "id"), array($this->keys[$bit["name"]], $this->keys["group_id"]))) {
                $this->errors[] = $this->errorMessages[$bit["name"]][$this::SIZE];
                $ok = false;
            }
        }
    }
    return $ok;
}

public function setPrices($bit) {
    if(isset($bit->choice)) {
        foreach ($bit->choice->children() as $value) {
            if(isset($value->price)) {
                $this->prices[(string) $bit->name][] = array("name" => (string) $value->name, "value" => (int) $value->price);
            }
        }
    } elseif(isset($bit->check)) {
        $this->prices[(string) $bit->name][] = array( "name" => "x", "value" => (int) $bit->check->price);
    }
}

}

?>
