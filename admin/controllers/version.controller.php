<?php
date_default_timezone_set('America/Guayaquil');
    Class VersionController{
        // TEMPLATE
        static public function version(){
            $version = "0.1";
            $day = date('d');
            $month = date('m');
            $year = date('Y');
            $time = time();
            // return "{$version}.{$day}.{$month}.{$year}.". date('His', $time);
            return "{$version}.{$day}.{$month}.{$year}.". date('Hi', $time);
        }
    }