<?php
    include 'inc/header.php';
    Session::checkSession();

    // Session Key Mismatch হ্যান্ডেল করা
    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");
    
    // Safety handling
    $userId = mysqli_real_escape_string($db->link, $userId);

    // ইউজারের সবকটি অ্যাক্টিভ/এপ্রুভড সাবস্ক্রিপশন নিয়ে আসার জন্য
    $subQuery = "SELECT s.*, c.category_name 
                 FROM tbl_subscription s 
                 LEFT JOIN tbl_category c ON s.category_id = c.id 
                 WHERE s.user_id = '$userId' 
                 AND LOWER(s.status) = 'approved' 
                 AND s.expire_date >= NOW() 
                 ORDER BY s.id DESC";

    $subData = $db->select($subQuery);

    // কোনো অ্যাক্টিভ সাবস্ক্রিপশন না থাকলে সাবস্ক্রিপশন কিনতে পাঠাবে
    if (!$subData || $subData->num_rows == 0) {
        header("Location: subscription.php");
        exit();
    }
?>

<div class="w-full max-w-5xl mx-auto my-10 px-4">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">আমার অ্যাক্টিভ পরীক্ষাসমূহ</h2>
            <p class="text-xs text-slate-500 mt-1">আপনার কেনা সাবস্ক্রিপশন অনুযায়ী নিচে পরীক্ষা দিন</p>
        </div>
        <a href="subscription.php" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold px-4 py-2.5 rounded-xl text-xs transition">
            <i class="fa-solid fa-plus"></i> নতুন কোর্স যোগ করুন
        </a>
    </div>

    <!-- Subscriptions Exam Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php 
            while ($subInfo = $subData->fetch_assoc()) {
                $catName = !empty($subInfo['category_name']) ? $subInfo['category_name'] : ($subInfo['exam_type'] ?? 'সাধারণ পরীক্ষা');
        ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Top Badge -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-indigo-50 text-indigo-600 text-[11px] font-bold px-3 py-1 rounded-lg border border-indigo-100">
                            <?php echo htmlspecialchars($subInfo['duration'] ?? 'Standard'); ?>
                        </span>
                        <span class="bg-emerald-100 text-emerald-700 font-semibold text-xs px-2.5 py-0.5 rounded-full border border-emerald-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            একটিভ
                        </span>
                    </div>

                    <!-- Exam Title -->
                    <h3 class="text-lg font-bold text-slate-800 mb-2">
                        <?php echo htmlspecialchars($catName); ?>
                    </h3>
                    
                    <!-- Expiry Date -->
                    <p class="text-xs text-slate-500 mb-6">
                        মেয়াদ শেষ: <span class="font-semibold text-slate-700"><?php echo date('d M Y', strtotime($subInfo['expire_date'])); ?></span>
                    </p>
                </div>

                <!-- Start Exam Button with Category ID -->
                <a href="starttest.php?cat_id=<?php echo $subInfo['category_id']; ?>" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                    <span>পরীক্ষা শুরু করুন</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        <?php } ?>
    </div>

</div>

<?php include 'inc/footer.php'; ?>