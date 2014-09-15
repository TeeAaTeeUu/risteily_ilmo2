<?php

include_once 'mysql.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

class database {

    private $mysql;

    public function __construct() {
        $this->mysql = new mysql();
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
    }

    public function get_mysql() {
        return $this->mysql;
    }

    public function put_category_to_db($category) {
        $this->mysql->put_query_from_array("category", $category);
    }

    public function put_person_to_db($person) {
        $this->mysql->put_query_from_array("person", $person);
        $temp = $this->mysql->get_query_select("*", "person", "nimi", $person["nimi"]);
        return $temp[0];
    }

    public function put_group_to_db($group) {
        $this->mysql->put_query_from_array("group", $group);
    }

    public function exists($where, $what, $is) {
        return $this->mysql->exists_in_db($where, $what, $is);
    }

    public function get_categorys_from_db() {
        return $this->mysql->get_query_select("*", "category");
    }

    public function get_groups_from_db_by_category_ids($category_ids) {
        $temp = array();
        foreach ($category_ids as $category_id) {
            $temp2 = $this->mysql->get_query_select("id", "group", "category_id", $category_id);
            if (empty($temp2)) {
                $temp[$category_id][] = -1;
            } else {
                foreach ($temp2 as $row) {
                    $temp[$category_id][] = $row["id"];
                }
            }
        }
        return $temp;
    }

    public function get_person_counts_from_db($group_ids) {
        $temp = $this->mysql->get_query_select("COUNT(nimi)", "person", "group_id", $group_ids, null, false);
        return $temp[0]["COUNT(nimi)"];
    }

    public function get_group_id_from_db($name) {
        $temp = $this->mysql->get_query_select("id", "group", "nimi", $name);
        return $temp[0]["id"];
    }

    public function get_persons_names_by_group_from_db($group_id) {
        $temp = $this->mysql->get_query_select("nimi, lempinimi", "person", "group_id", $group_id);
        $temp2 = array();

        foreach ($temp as $person) {
            if ($person["lempinimi"] != "")
                $temp2[] = $person["lempinimi"];
            else
                $temp2[] = $person["nimi"];
        }
        return $temp2;
    }

    public function get_groups_from_db($category_id) {
        return $this->mysql->get_query_select("*", "group", "category_id", $category_id);
    }

    public function get_group_luokka_from_db($group_id) {
        $temp = $this->mysql->get_query_select("luokka", "group", "id", $group_id);
        return $temp[0]["luokka"];
    }

    public function get_category_id_from_db($group_id) {
        $temp = $this->mysql->get_query_select("category_id", "group", "id", $group_id);
        return $temp[0]["category_id"];
    }

    public function get_group_name_from_db($group_id) {
        $temp = $this->mysql->get_query_select("nimi", "group", "id", $group_id);
        return $temp[0]["nimi"];
    }

    public function amount_of($from, $where, $is) {
        $temp = $this->mysql->get_query_select("COUNT(*)", $from, $where, $is);
        return $temp[0]["COUNT(*)"];
    }

    public function table_exists_in_db($table) {
        return $this->mysql->table_exists_in_db($table);
    }

    public function exists_id_in_person($viite) {
        return $this->exists("person", "viite", $viite);
    }

    public function update_db($array, $table, $where = null, $is = null) {
        return $this->mysql->update_db($array, $table, $where, $is);
    }

    public function get_unique_viite() {
        do {
            $viite = $this->hae_viite();
        } while ($this->exists_id_in_person($viite));
        return $viite;
    }

    private function laske_tarkiste($viite) { // http://pastebin.com/f69d0ce0f
        $kertoimet = array(7, 3, 1);
        $pituus = strlen($viite);
        $viite = str_split($viite);
        $summa = 0;
        for ($i = $pituus - 1; $i >= 0; --$i) {
            $summa += $viite[$i] * $kertoimet[($pituus - 1 - $i) % 3];
        }
        return (10 - $summa % 10) % 10;
    }

    private function hae_viite() { // http://pastebin.com/f69d0ce0f
        $tulos = '';
        $viite = mt_rand(100, 999999);
        $viite = $viite . $this->laske_tarkiste($viite);
        return $viite;
    }

}
?>