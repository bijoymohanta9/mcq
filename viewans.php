<?php include 'inc/header.php'; ?>
<?php
Session::checkSession();

// কাস্টম পরীক্ষায় সেশন থেকে প্রশ্নসমূহ ও মোট সংখ্যা নেওয়া
$examQuestions = Session::get("exam_questions");
$total = Session::get("exam_total_ques") ? count($examQuestions) : $exm->getTotalRows();
?>

<div class="max-w-4xl mx-auto my-10 px-4">
    
    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-circle-check text-indigo-600"></i>
                <span>প্রশ্ন ও সঠিক উত্তরসমূহ</span>
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">সবগুলো প্রশ্নের সঠিক উত্তর নিচে হাইলাইট করে দেওয়া হলো</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-100 px-4 py-2 rounded-xl text-center">
            <span class="text-xs text-indigo-500 font-bold block uppercase">মোট প্রশ্ন</span>
            <span class="text-xl font-black text-indigo-700"><?php echo $total; ?></span>
        </div>
    </div>

    <!-- Questions Container -->
    <div class="space-y-6 mb-8">
        <?php 
            // যদি সেশনে কাস্টম প্রশ্ন থেকে থাকে তবে সেগুলো দেখাবে, অন্যথায় সব প্রশ্ন
            if ($examQuestions && !empty($examQuestions)) {
                $count = 0;
                foreach ($examQuestions as $quesNo) {
                    $count++;
                    $question = $exm->getQuesByNumber($quesNo);
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
                                $isRight = ($result['rightAns'] == '1');
                    ?>
                        <div class="flex items-center gap-3 p-3.5 rounded-xl border transition <?php echo $isRight ? 'border-emerald-300 bg-emerald-50/50 text-emerald-900 font-bold' : 'border-slate-100 text-slate-600'; ?>">
                            <div class="w-5 h-5 rounded-full border flex items-center justify-center <?php echo $isRight ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300'; ?>">
                                <?php if ($isRight) { ?>
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                <?php } ?>
                            </div>
                            <span class="text-xs sm:text-sm">
                                <?php echo htmlspecialchars($result['ans']); ?>
                            </span>
                            <?php if ($isRight) { ?>
                                <span class="ml-auto text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800">
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
            } else {
                // সেশন না থাকলে ডিফল্ট সব প্রশ্ন ডাটাবেজ থেকে পড়া
                $getQues = $exm->getQueByOrder();
                if ($getQues) {
                    while ($question = $getQues->fetch_assoc()) {
                        $quesNo = $question['quesNo'];
        ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-100 p-5">
                    <h3 class="text-base font-bold text-slate-800">
                        <span class="text-indigo-600 font-black mr-1">Q<?php echo $quesNo; ?>.</span> 
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
                        <div class="flex items-center gap-3 p-3.5 rounded-xl border transition <?php echo $isRight ? 'border-emerald-300 bg-emerald-50/50 text-emerald-900 font-bold' : 'border-slate-100 text-slate-600'; ?>">
                            <div class="w-5 h-5 rounded-full border flex items-center justify-center <?php echo $isRight ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300'; ?>">
                                <?php if ($isRight) { ?>
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                <?php } ?>
                            </div>
                            <span class="text-xs sm:text-sm">
                                <?php echo htmlspecialchars($result['ans']); ?>
                            </span>
                            <?php if ($isRight) { ?>
                                <span class="ml-auto text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800">
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
    <div class="flex justify-center pb-8">
        <a href="starttest.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center gap-2 text-sm">
            <i class="fa-solid fa-rotate-right"></i>
            <span>নতুন পরীক্ষা শুরু করুন</span>
        </a>
    </div>

</div>

<?php include 'inc/footer.php'; ?>