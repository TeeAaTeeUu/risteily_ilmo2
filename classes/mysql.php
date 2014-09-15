<?php

include_once 'settings.php';
include_once 'helper.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

class mysql {

    private $local_db;
    public $dbname;
    private $dbuser;
    private $dbpw;
    private $dbhost;
    public $etuliite;
    private $dbsocket;
    private $dbport;

    public function __construct() {
        $this->dbhost = get_dbhost();
        $this->dbuser = get_dbuser();
        $this->dbpw = get_dbpw();
        $this->dbname = get_dbname();
        $this->dbsocket = get_dbsocket();
        $this->dbport = get_dbport();
        $this->etuliite = get_etuliite();
        
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');

        if (empty($this->dbsocket)) {
            $this->local_db = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpw) or die();
            mysqli_select_db($this->local_db, $this->dbname) or die();
        } else {
            $this->local_db = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpw, $this->dbname, $this->dbport, $this->dbsocket) or die();
        }
    }

    public function connection() {
        return $this->local_db;
    }

    public function exists_in_db($from, $where, $is) {
        $data_array = $this->get_query_select('*', $from, $where, $is);

        $where = clean($where);
        if (is_array($where) and isset($data_array[0]))
            return true;
        elseif (isset($data_array[0]) and $data_array[0][$where] !== "")
            return true;
        else
            return false;
    }

    public function table_exists_in_db($table) {
        $table = $this->etuliite . $this->filterParameters($table);
        return $this->get_query_bulk("SELECT * FROM $table", true);
    }

    public function get_query_select($what, $from, $where = null, $is = null, $order_by = null, $where_array_is_and_not_or = true, $asc = null, $how_much = null) {
        $what = $this->filterParameters($what);
        $where = $this->filterParameters($where);
        $order_by = $this->filterParameters($order_by);
        $from = $this->filterParameters($from);
        $is = $this->filterParameters($is);

        $query = "SELECT " . clean($what) . " FROM " . $this->etuliite . $from;

        if (!empty($where)) {
            if (is_array($where) and is_array($is)) {
                $query .= " WHERE " . $this->get_where_query_part_from_array($where, $is, $where_array_is_and_not_or, true);
            } elseif (is_array($is)) {
                $query .= " WHERE " . $this->get_where_query_part_from_array($where, $is, $where_array_is_and_not_or, false);
            }
            else
                $query .= " WHERE " . clean($where) . "='$is'";
        }

        if (!empty($order_by))
            $query .= " ORDER BY " . clean($order_by);

        if (!empty($asc)) {
            if ($asc == true) {
                $query .= " ASC";
            } else {
                $query .= " DESC";
            }
        }

        if (!empty($how_much))
            $query .= " LIMIT " . $how_much;

        return $this->get_query_bulk($query);
    }

    public function get_where_query_part_from_array($where_array, $is_array, $where_array_is_and_not_or, $where_is_array = true) {
        $temp_query = "";
        $first = true;
        if ($where_is_array) {
            for ($i = 0; $i <= count($where_array) - 1; $i++) {
                if (!$first) {
                    if ($where_array_is_and_not_or)
                        $temp_query .= " AND ";
                    else
                        $temp_query .= " OR ";
                }

                $temp_query .= clean($where_array[$i]) . "='$is_array[$i]'";

                $first = false;
            }
        } else {
            $temp_query .= clean($where_array) . " IN (";
            for ($i = 0; $i <= count($is_array) - 1; $i++) {
                if (!$first) {
                    $temp_query .= ", ";
                }
                $temp_query .= "'" . $is_array[$i] . "'";
                $first = false;
            }
            $temp_query .= ")";
        }
        return $temp_query;
    }

    public function get_query_bulk($query, $test = false) {
        if ($test) {
            $result = @mysqli_query($this->local_db, $query);
            if (!$result) {
                return false;
            } else {
                return true;
            }
        } else {
            $result = mysqli_query($this->local_db, $query) or die(mysqli_error($this->local_db));
        }
        $n = 0;
        $template_array = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $template_array[$n] = $row;
            ++$n;
        };

        return $template_array;
    }

    public function put_query_from_array($where, $what_is_what_array) {
        $what_is_what_array = $this->filterParameters($what_is_what_array);
        $where = $this->filterParameters($where);
        $query = "INSERT INTO " . $this->etuliite . $where . " (" . implode(", ", clean(array_keys($what_is_what_array))) . ") VALUES ('" . implode("', '", array_values($what_is_what_array)) . "')";

        mysqli_query($this->local_db, $query) or die();
    }

    public function delete_query_from_array($where, $what, $is) {
        $what = $this->filterParameters($what);
        $is = $this->filterParameters($is);
        $where = $this->filterParameters($where);
        $query = "DELETE FROM " . $this->etuliite . $where . " WHERE " . clean($what) . "=" . $is . ";";

        mysqli_query($this->local_db, $query) or die();
    }

    public function put_query_bulk($query) {
        return mysqli_query($this->local_db, $query) or die();
    }

    public function update_db($array, $table, $where = null, $is = null) {
        $array = $this->filterParameters($array);
        $table = $this->filterParameters($table);
        $where = $this->filterParameters($where);
        $is = $this->filterParameters($is);

        $query = "UPDATE " . $this->etuliite . $table . " SET ";

        $first = true;
        foreach ($array as $key => $value) {
            if (!$first)
                $query .= ", ";

            $query .= clean($key) . "='$value'";
            $first = false;
        }
        if (isset($where))
            $query .= "WHERE " . clean($where) . "='$is'";

        mysqli_query($this->local_db, $query) or die();
    }

    private function filterParameters($array) {
        /*
         * Created by: Stefan van Beusekom
         * Created on: 31-01-2011
         * Description: A method that ensures safe data entry, and accepts either strings or arrays. If the array is multidimensional,
         * it will recursively loop through the array and make all points of data safe for entry.
         * parameters: string or array;
         * return: string or array;
         */

        // Check if the parameter is an array
        if (is_array($array)) {
            // Loop through the initial dimension
            foreach ($array as $key => $value) {
                // Check if any nodes are arrays themselves
                if (is_array($array[$key]))
                // If they are, let the function call itself over that particular node
                    $array[$key] = $this->filterParameters($array[$key]);

                // Check if the nodes are strings
                if (is_string($array[$key]))
                // If they are, perform the real escape function over the selected node
                    $array[$key] = htmlspecialchars(mysqli_real_escape_string($this->local_db, $array[$key]), ENT_QUOTES, "UTF-8");
            }
        }
        // Check if the parameter is a string
        if (is_string($array))
        // If it is, perform a mysql_real_escape_string on the parameter
            $array = htmlspecialchars(mysqli_real_escape_string($this->local_db, $array), ENT_QUOTES, "UTF-8");

        // Return the filtered result
        return $array;
    }

}

?>