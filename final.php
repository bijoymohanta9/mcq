<?php include 'inc/header.php'; ?>
<?php
Session::checkSession();

$score = isset($_SESSION['score']) ? (int)$_SESSION['score'] : 0;
$total = Session::get("exam_total_ques") ? (int)Session::get("exam_total_ques") : 0;
$category_id = Session::get("exam_category_id") ? (int)Session::get("exam_category_id") : 0;
$userId = Session::get("userId");

// স্কোর ডাটাবেজে সেভ করা (যদি মেথড থাকে)
if ($total > 0 && method_exists($exm, 'saveScore')) {
    $exm->saveScore($userId, $category_id, $score, $total);
}

// শতকরা নম্বর গণনা
$percentage = ($total > 0) ? round(($score / $total) * 100) : 0;

// পারফরম্যান্স এর ওপর ভিত্তি করে ব্যাজ ও কালার
if ($percentage >= 80) {
    $statusMsg = "অসাধারণ পারফরম্যান্স!";
    $badgeColor = "bg-emerald-100 text-emerald-700 border-emerald-300";
    $icon = "fa-trophy text-amber-500";
} elseif ($percentage >= 50) {
    $statusMsg = "ভালো হয়েছে, আরও উন্নতি সম্ভব!";
    $badgeColor = "bg-indigo-100 text-indigo-700 border-indigo-300";
    $icon = "fa-star text-indigo-500";
} else {
    $statusMsg = "আরও বেশি অনুশীলনের প্রয়োজন!";
    $badgeColor = "bg-rose-100 text-rose-700 border-rose-300";
    $icon = "fa-circle-exclamation text-rose-500";
}

// পরীক্ষার সেশনগুলো পরিষ্কার করা
unset($_SESSION['score']);
unset($_SESSION['exam_questions']);
unset($_SESSION['exam_total_ques']);
unset($_SESSION['exam_time_limit']);
unset($_SESSION['exam_start_time']);
unset($_SESSION['exam_category_id']);
?>

<div class="max-w-2xl mx-auto my-12 px-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden text-center p-8 sm:p-10">
        
        <!-- Animated Icon Header -->
        <div class="w-20 h-20 mx-auto rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-4xl shadow-inner mb-6">
            <i class="fa-solid <?php echo $icon; ?>"></i>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
            পরীক্ষা সম্পন্ন হয়েছে!
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-2 font-medium">
            <?php echo $statusMsg; ?>
        </p>

        <!-- Score Card Display -->
        <div class="my-8 p-6 bg-slate-50 rounded-2xl border border-slate-200/80 max-w-md mx-auto">
            <span class="text-xs uppercase font-bold tracking-widest text-slate-400 block mb-1">
                চূড়ান্ত স্কোর
            </span>
            <div class="text-4xl sm:text-5xl font-black text-indigo-600 my-2">
                <?php echo $score; ?> <span class="text-xl font-bold text-slate-400">/ <?php echo $total; ?></span>
            </div>
            
            <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo $badgeColor; ?>">
                <i class="fa-solid fa-chart-line"></i>
                <span>সাফল্যের হার: <?php echo $percentage; ?>%</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
            <a href="viewans.php" class="w-full sm:w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 px-5 rounded-xl transition flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-eye text-slate-500"></i>
                <span>উত্তরপত্র দেখুন</span>
            </a>
            
            <a href="starttest.php" class="w-full sm:w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-5 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-rotate-right"></i>
                <span>পুনরায় পরীক্ষা দিন</span>
            </a>
        </div>

    </div>
</div>

<?php include 'inc/footer.php'; ?>