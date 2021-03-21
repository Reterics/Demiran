<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 3/20/2021
 * Time: 8:20 PM
 */



class Settings {
    public $_settings = array();
    public $_last_sql_error = "";

    function __construct(){

    }

    function loadSettings(){
        global $connection;
        $sql = "SELECT * FROM settings;";
        if (isset($connection) && $connection) {
            $result = mysqli_query($connection, $sql);

            while ($row = mysqli_fetch_array($result)) {
                if (isset($row['id']) && isset($row['message']) && isset($row['setting_name'])) {
                    array_push($this->_settings, $row);
                }
            }
        } else {
            $this->_last_sql_error = "No SQL Connection found";
        }
    }

    function getHighestId() {
        $index = 1;
        $max = 1;
        foreach($this->_settings as $setting) {
            $id = intval($setting["id"]);

            if($max < $id) {
                $max = $id;
            }
            $index = $index + 1;
        }

        if ($max > $index) {
            return $max;
        } else {
            return $index;
        }
    }

    function saveSetting($setting) {
        global $connection;
        if (!$connection) {
            return false;
        }
        if (!isset($setting)) {
            return false;
        }

        if(!isset($setting["id"])) {
            $id = $setting["id"];
        } else {
            $id = $this->getHighestId();
        }

        if(isset($setting["setting_name"])) {
            $setting_name = $setting["setting_name"];
        } else {
            return false;
        }
        if(isset($setting["message"])) {
            $message = $setting["message"];
        } else {
            return false;
        }
        if(isset($setting["extra"])) {
            $extra = $setting["extra"];
        } else {
            $extra = "";
        }

        $sql = "INSERT IGNORE INTO settings SET id=".$id.",setting_name='".$setting_name."',message='".$message."',extra='".$extra."';";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            return true;
        } else {
            $this->_last_sql_error = mysqli_error($connection);
            return false;
        }
    }

    function saveAllSettings() {
        $bool = true;
        foreach($this->_settings as $setting) {
            if ($bool == true){
                $bool = $this->saveSetting($setting);
            }
        }
        return $bool;
    }

    function getSettingByName($name) {
        $selected = null;
        foreach($this->_settings as $setting) {
            if (isset($setting['setting_name']) && $setting['setting_name'] == $name) {
                $selected = $setting;
            }
        } return $selected;
    }

    function getSettingValueByName($name) {
        $selected = $this->getSettingByName($name);
        if ($selected && isset($selected["message"])) {
            return $selected["message"];
        } else {
            return "";
        }
    }
}