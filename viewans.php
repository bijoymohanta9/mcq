<?php 
include 'inc/header.php';
Session::checkSession();

// সেশন থেকে ইউজারের সিলেক্ট করা উত্তরসমূহ লোড করা
$userAnswers   = Session::get("user_ans") ? Session::get("user_ans") : [];
$examQuestions = Session::get("exam_questions");

// URL থেকে ট্র্যাকিং কোড রিড করা
$attemptCode   = isset($_GET['code']) ? mysqli_real_escape_string($db->link, $_GET['code']) : '';

$categoryId = null;
$subjectId  = null;
$total      = null;

// ১. হিস্ট্রি টেবিল থেকে অ্যাটেম্পটের বিবরণ ও ক্যাটাগরি/সাবজেক্ট ফিল্টার লোড করা
if (!empty($attemptCode)) {
    $historyQuery = "SELECT total_questions, category_id, subject_id FROM tbl_exam_history WHERE attempt_code = '$attemptCode'";
    $historyData  = $db->select($historyQuery);
    if ($historyData) {
        $hRow       = $historyData->fetch_assoc();
        $total      = (int)$hRow['total_questions'];
        $categoryId = (int)$hRow['category_id'];
        $subjectId  = (int)$hRow['subject_id'];
    }
} 

// ২. সেশন বা ফলব্যাক থেকে মোট প্রশ্ন সেট করা
if (!$total) {
    if ($examQuestions && is_array($examQuestions)) {
        $total = count($examQuestions);
    } else {
        $total = Session::get("exam_total_ques") ? Session::get("exam_total_ques") : $exm->getTotalRows();
    }
}
?>

<div class="max-w-4xl mx-auto my-10 px-4">
    
    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-square-poll-vertical text-indigo-600"></i>
                <span>উত্তর পুনঃনিরীক্ষা ও সঠিক উত্তরসমূহ</span>
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">আপনার প্রদত্ত উত্তর ও সঠিক উত্তরের বিস্তারিত পর্যালোচনা</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-xl text-center">
                <span class="text-xs text-indigo-500 font-bold block uppercase">মোট প্রশ্ন</span>
                <span class="text-xl font-black text-indigo-700"><?php echo $total; ?></span>
            </div>
            <a href="exam_history.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-3 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> হিস্ট্রিতে ফিরুন
            </a>
        </div>
    </div>

    <!-- Questions Container -->
    <div class="space-y-6 mb-8">
        <?php 
            // অপশন A: যদি সেশনে পরীক্ষার প্রশ্ন আইডি সংরক্ষিত থাকে
            if ($examQuestions && !empty($examQuestions)) {
                $count = 0;
                foreach ($examQuestions as $quesNo) {
                    $count++;
                    $question = $exm->getQuesByNumber($quesNo);
                    $selectedAnsId = isset($userAnswers[$quesNo]) ? $userAnswers[$quesNo] : null;

                    if ($question) {
        ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Question Title Header -->
                <div class="bg-slate-50 border-b border-slate-100 p-5">
                    <h3 class="text-base font-bold text-slate-800 leading-relaxed">
                        <span class="text-indigo-600 font-black mr-1">Q<?php echo $count; ?>.</span> 
                        <?php echo htmlspecialchars($question['ques']); ?>
                    </h3>
                </div>

                <!-- Answer List -->
                <div class="p-5 space-y-2.5">
                    <?php 
                        $answer = $exm->getAnswer($quesNo);
                        if ($answer) {
                            while ($result = $answer->fetch_assoc()) {
                                $isRight    = ($result['rightAns'] == '1');
                                $isSelected = ($selectedAnsId == $result['id']);

                                $cardStyle = "border-slate-100 text-slate-600";
                                if ($isRight) {
                                    $cardStyle = "border-emerald-300 bg-emerald-50/60 text-emerald-900 font-semibold";
                                } elseif ($isSelected && !$isRight) {
                                    $cardStyle = "border-rose-300 bg-rose-50/60 text-rose-900 font-semibold";
                                }
                    ?>
                        <div class="flex items-center justify-between p-3.5 rounded-xl border transition <?php echo $cardStyle; ?>">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] <?php echo $isRight ? 'border-emerald-600 bg-emerald-600 text-white' : ($isSelected ? 'border-rose-600 bg-rose-600 text-white' : 'border-slate-300'); ?>">
                                    <?php if ($isRight): ?>
                                        <i class="fa-solid fa-check"></i>
                                    <?php elseif ($isSelected): ?>
                                        <i class="fa-solid fa-xmark"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs sm:text-sm">
                                    <?php echo htmlspecialchars($result['ans']); ?>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <?php if ($isSelected): ?>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full <?php echo $isRight ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800'; ?>">
                                        আপনার উত্তর
                                    </span>
                                <?php endif; ?>

                                <?php if ($isRight): ?>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-emerald-600 text-white">
                                        সঠিক উত্তর
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                            }
                        } 
                    ?>
                </div>
            </div>
        <?php 
                    }
                }
            } else {
                // অপশন B: সেশন না থাকলে নির্দিষ্ট Category ও Subject ফিল্টার করে ডাটাবেজ থেকে প্রশ্ন লোড করা
                $conditions = ["isDeleted = 0"];
                if ($categoryId && $categoryId > 0) {
                    $conditions[] = "category_id = '$categoryId'";
                }
                if ($subjectId && $subjectId > 0) {
                    $conditions[] = "subject_id = '$subjectId'";
                }

                $whereClause = "WHERE " . implode(" AND ", $conditions);
                $limitClause = $total ? "LIMIT $total" : "";

                $getQues = $db->select("SELECT * FROM tbl_ques $whereClause ORDER BY quesNo ASC $limitClause");

                if ($getQues) {
                    $count = 0;
                    while ($question = $getQues->fetch_assoc()) {
                        $count++;
                        $quesNo = $question['quesNo'];
        ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-100 p-5">
                    <h3 class="text-base font-bold text-slate-800">
                        <span class="text-indigo-600 font-black mr-1">Q<?php echo $count; ?>.</span> 
                        <?php echo htmlspecialchars($question['ques']); ?>
                    </h3>
                </div>
                <div class="p-5 space-y-2.5">
                    <?php 
                        $answer = $exm->getAnswer($quesNo);
                        if ($answer) {
                            while ($result = $answer->fetch_assoc()) {
                                $isRight = ($result['rightAns'] == '1');
                    ?>
                        <div class="flex items-center justify-between p-3.5 rounded-xl border transition <?php echo $isRight ? 'border-emerald-300 bg-emerald-50/50 text-emerald-900 font-semibold' : 'border-slate-100 text-slate-600'; ?>">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] <?php echo $isRight ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300'; ?>">
                                    <?php if ($isRight) { ?><i class="fa-solid fa-check"></i><?php } ?>
                                </div>
                                <span class="text-xs sm:text-sm">
                                    <?php echo htmlspecialchars($result['ans']); ?>
                                </span>
                            </div>
                            <?php if ($isRight) { ?>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800">
                                    সঠিক উত্তর
                                </span>
                            <?php } ?>
                        </div>
                    <?php 
                            }
                        } 
                    ?>
                </div>
            </div>
        <?php 
                    }
                }
            } 
        ?>
    </div>

    <!-- Bottom Action Button -->
    <div class="flex justify-center gap-4 pb-8">
        <a href="exam_history.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3.5 px-6 rounded-xl transition text-sm flex items-center gap-2">
            <i class="fa-solid fa-list-check"></i>
            <span>সব রেকর্ড দেখুন</span>
        </a>
        <a href="starttest.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center gap-2 text-sm">
            <i class="fa-solid fa-rotate-right"></i>
            <span>নতুন পরীক্ষা দিন</span>
        </a>
    </div>

</div>

<?php include 'inc/footer.php'; ?>