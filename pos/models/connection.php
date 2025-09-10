<?php
    class Connection{
        static public function connect(){
            $host ="localhost";//SERVER
            $db = "siona_pos"; //DB NAME
            $user = "root"; //USER
            $password =""; //PASSWORD
            $link = new PDO("mysql:host=$host;dbname=$db;charset=UTF8", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]);
            $link->exec("set names utf8");
            return $link;
        }
    }