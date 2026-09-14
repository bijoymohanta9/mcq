<?php 
include 'inc/header.php';
Session::checkSession();

$userId = Session::get("userid");
$subQuery = "SELECT * FROM tbl_subscription 
             WHERE user_id = '$userId' 
             AND status = 'active' 
             AND expire_date >= NOW() 
             LIMIT 1";

$hasSubscription = $db->select($subQuery);

if (!$hasSubscription) {
    // অ্যাক্টিভ সাবস্ক্রিপশন না থাকলে সাবস্ক্রিপশন পেজে রিডাইরেক্ট করে দেওয়া হবে
    header("Location: subscription.php");
    exit();
}
?>
<?php
Session::checkSession();
?>
<?php
// ইউজার অ্যাকাউন্ট সক্রিয় কিনা চেক করার জন্য
$userId = Session::get("userid");
$checkSubQuery = "SELECT * FROM tbl_subscription 
                  WHERE user_id = '$userId' 
                  AND status = 'active' 
                  AND expire_date >= NOW() 
                  ORDER BY id DESC LIMIT 1";

$hasSubscription = $db->select($checkSubQuery);

if (!$hasSubscription) {
    header("Location: subscription.php");
    exit();
}
?>
<div class="main">
<h1>Welcome to Online Exam - Start Now</h1>
	<div class="segment" style="margin-right:30px;">
		<img src="img/online_exam.png"/>
	</div>
	<div class="segment">
	<h2>Start Test</h2>
	<ul>
		<li><a href="starttest.php">Start Now...</a></li>
	</ul>
	</div>
	
  </div>
<?php include 'inc/footer.php'; ?>