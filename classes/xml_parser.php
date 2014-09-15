<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_log', 'script_errors.log');
//ini_set('log_errors', 'On');

class xml_parser {

    private $xml_string;
    private $xml;
    private $message;

    const PERSON = "person";
    const GROUP = "group";
    const CATEGORY = "category";
    const NAME = "name";
    const INFO = "info";
    const LENGTH = "length";
    const ERRORS = "length";
    const MUSTHAVE = "musthave";
    const CHOICE = "choice";
    const CHECK = "check";
    const CONTENT = "content";
    const AUTOFILL = "autofill";
    const TEXT = "text";
    const TEXTAREA = "textarea";
    const YES = "yes";

//    private static $xpathDB;

    public function __construct($xml = null) {
        if ($xml == null)
            $xml = getcwd() . "/settings.xml";
        $xml = file_get_contents($xml);
        $this->giveXml($xml);
//        $this->xpathDB = array();
    }

    public function exists($what) {
        return isset($this->xml->$what);
    }

    public function name($what) {
        if ($this->exists($what))
            return $this->xml->$what->name;
        return false;
    }

    public function bitInfo($what) {
        return $bit->$what;
    }

    public function bitType($bit) {
        if (isset($bit->content))
            return $this::AUTOFILL;
        elseif (isset($bit->group))
            return $this::GROUP;
        elseif (isset($bit->choice))
            return $this::CHOICE;
        elseif (isset($bit->check))
            return $this::CHECK;
        elseif ($bit->length->max > 150)
            return $this::TEXTAREA;
        else
            return $this::TEXT;
    }

    public function informationBits($what, $i = null) {
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
        if (isset($this->xml->$what->information)) {
            if ($i == null)
                return $this->xml->$what->information->children();
            else
                return $this->xml->$what->information->bit[$i];
            return false;
        }
        else
            return array();
    }

    public function autofillRows($what, $i = null) {
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
        if (isset($this->xml->$what->autofill)) {
            if ($i == null)
                return $this->xml->$what->autofill->children();
            else
                return $this->xml->$what->autofill->row[$i];
            return false;
        }
        else
            return array();
    }

    public function currency() {
        return (string) $this->xml->others->currency;
    }

    public function startTime() {
        return (int) $this->xml->others->open->starttime->unixtime;
    }

    public function stopTime() {
        return (int) $this->xml->others->open->stoptime->unixtime;
    }

    public function debug() {
        if ((string) $this->xml->others->debug == "yes")
            return true;
        return false;
    }
    
    public function getSubject($what) {
        return (string) $this->xml->$what->subject;
    }
    
    public function getInfo($what) {
        return (string) $this->xml->$what->info;
    }
    
    public function earlyMessage() {
        $this->loopOpenErrorMessages();
        return $this->message["early"];
    }
    
    public function lateMessage() {
        $this->loopOpenErrorMessages();
        return $this->message["late"];
    }
    
    public function payInfo() {
        return (string) $this->xml->others->pay->info;
    }
    
    public function emailInfo() {
        return (string) $this->xml->others->email->info;
    }

    //private methods start here
//    private static function isItSet($what) {
//        if (!array_key_exists($what, $this->xpathDB)) {
//            $temp = $this->xml->xpath("/settings" . $what);
//            if (empty($temp))
//                $this->xpathDB[$what] = false;
//            else
//                $this->xpathDB[$what] = true;
//        }
//        return $this->xpathDB[$what];
//    }

    private function giveXml($xml) {
        $this->xml_string = $xml;

        $this->xml = new SimpleXMLElement($this->xml_string);
    }
    
    private function loopOpenErrorMessages() {
        foreach ($this->xml->others->open->errors->children() as $error) {
            $this->message[(string) $error->name] = (string) $error->message;
        }
    }

}

?>