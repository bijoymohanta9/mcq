<?php
class Session{
     public static function init(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
     }
     
     public static function set($key, $val){
        self::init();
        $_SESSION[$key] = $val;
     }

     public static function get($key){
        self::init();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
     }

     public static function checkAdminSession(){
        self::init();
        if (self::get("adminLogin") == false) {
            self::destroy();
            header("Location:login.php");
            exit();
        }
     }

     public static function checkAdminLogin(){
        self::init();
        if (self::get("adminLogin") == true) {
            header("Location:index.php");
            exit();
        }
     }

     public static function checkSession(){
        self::init(); // সেশন ইনিশিয়ালাইজ করা বাধ্যতামূলক
        if (self::get("login") == false) {
            self::destroy();
            header("Location:index.php");
            exit();
        }
     }

     public static function checkLogin(){
        self::init();
        if (self::get("login") == true) {
            // লগইন করা থাকলে সাবস্ক্রিপশন বা এক্সাম পেজে রিডাইরেক্ট হবে
            header("Location: subscription.php");
            exit();
        }
     }

     public static function destroy(){
        self::init();
        session_destroy();
        session_unset();
     }
}
?>