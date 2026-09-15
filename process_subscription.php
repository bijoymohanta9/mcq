<?php
$filepath = realpath(dirname(__FILE__));

include_once ($filepath . '/lib/Session.php');
Session::checkSession();

include_once ($filepath . '/lib/Database.php');

if (file_exists('helpers/Format.php')) {
    include_once 'helpers/Format.php';
} elseif (file_exists('lib/Format.php')) {
    include_once 'lib/Format.php';
}

$db = new Database();
$fm = new Format();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userId = Session::get("userid");
    if (!$userId) {
        echo "unauthorized";
        exit();
    }

    // ১. category_id রিসিভ ও স্যানিটাইজ করা
    $categoryId = $fm->validation($_POST['category_id'] ?? '');
    $duration   = $fm->validation($_POST['plan'] ?? '');
    $amount     = $fm->validation($_POST['amount'] ?? '');

    $categoryId = mysqli_real_escape_string($db->link, $categoryId);
    $duration   = mysqli_real_escape_string($db->link, $duration);
    $amount     = mysqli_real_escape_string($db->link, $amount);

    if (empty($categoryId)) {
        echo "ক্যাটাগরি সিলেক্ট করুন!";
        exit();
    }

    // ২. প্ল্যান অনুযায়ী মেয়াদ নির্ধারণ
    $monthsToAdd = 6;
    if ($duration === '3_months') {
        $monthsToAdd = 3;
    } elseif ($duration === '1_year' || $duration === '12_months') {
        $monthsToAdd = 12;
    }

    $startDate  = date('Y-m-d H:i:s');
    $expireDate = date('Y-m-d H:i:s', strtotime("+$monthsToAdd months"));

    // ৩. category_id কলামে ডাটা ইনসার্ট ক্যোয়ারি
    $query = "INSERT INTO tbl_subscription (user_id, category_id, duration, amount, status, start_date, expire_date) 
              VALUES ('$userId', '$categoryId', '$duration', '$amount', 'active', '$startDate', '$expireDate')";

    $insert = $db->insert($query);

    if ($insert) {
        echo "success";
    } else {
        echo "error";
    }
}
?>