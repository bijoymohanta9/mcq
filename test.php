<?php include 'inc/header.php'; ?>
<?php
    Session::checkSession();

    // ১. সেশন থেকে ডাটা রিড করা
    $examQuestions = Session::get("exam_questions"); // এতে প্রশ্নের primary key 'id' (যেমন: 66, 67, 68) থাকা উচিত
    $totalQues     = Session::get("exam_total_ques");
    $catId         = Session::get("exam_category_id");
    $subId         = Session::get("exam_subject_id");

    // টাইমার ক্যালকুলেশন
    $timeLimit        = Session::get("exam_time_limit") ? (int)Session::get("exam_time_limit") : 5; // মিনিটে
    $startTime        = Session::get("exam_start_time") ? (int)Session::get("exam_start_time") : time();
    $totalSeconds     = $timeLimit * 60;
    $elapsedSeconds   = time() - $startTime;
    $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

    // ২. ফর্ম সাবমিট প্রসেসিং
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
        $currentNumber = (int)$_POST['number'];
        $selectedAns   = isset($_POST['ans']) ? (int)$_POST['ans'] : 0;

        // বর্তমান প্রশ্নের আসল Primary Key ID বের করা
        if (isset($examQuestions[$currentNumber - 1])) {
            $quesId = $examQuestions[$currentNumber - 1]; // যেমন: 68
            
            // উত্তর প্রসেস করা (Score / Correct Answer আপডেট)
            if (method_exists($exm, 'processAnswer')) {
                $exm->processAnswer($quesId, $selectedAns);
            }
        }

        // শেষ প্রশ্ন হলে final.php, না হলে পরবর্তী প্রশ্ন (q + 1)
        if ($currentNumber >= $totalQues) {
            header("Location: final.php");
            exit();
        } else {
            $next = $currentNumber + 1;
            header("Location: test.php?q=" . $next);
            exit();
        }
    }

    // ৩. বর্তমান প্রশ্নের সিকোয়েন্স নম্বর (q = 1, 2, 3...)
    $number = isset($_GET['q']) ? (int)$_GET['q'] : 1;

    if (!$examQuestions || $number > $totalQues || !isset($examQuestions[$number - 1])) {
        header("Location: final.php");
        exit();
    }

    // $quesId হলো tbl_ques টেবিলের মূল Primary Key 'id' (যেমন: 68)
    $quesId   = $examQuestions[$number - 1];
    
    // Primary Key 'id' দিয়ে প্রশ্ন ফেচ করা
    if (method_exists($exm, 'getQuestionById')) {
        $question = $exm->getQuestionById($quesId);
    } else {
        $question = $exm->getQuestionByNumber($quesId);
    }

    // ক্যাটাগরি ও সাবজেক্টের নাম ফেচ করা
    $categoryName = "সকল ক্যাটাগরি";
    $subjectName  = "সকল বিষয়";

    if ($catId > 0 && method_exists($exm, 'getCategoryById')) {
        $catData = $exm->getCategoryById($catId);
        if ($catData && $row = $catData->fetch_assoc()) {
            $categoryName = $row['category_name'] ?? $row['name'];
        }
    }

    if ($subId > 0 && method_exists($exm, 'getSubjectById')) {
        $subData = $exm->getSubjectById($subId);
        if ($subData && $row = $subData->fetch_assoc()) {
            $subjectName = $row['subject_name'] ?? $row['name'];
        }
    }
?>

<div class="max-w-3xl mx-auto my-8 px-4">
    
    <!-- Category & Subject Header Badge -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4 bg-slate-100 p-3 rounded-xl border border-slate-200">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
            <span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-lg border border-indigo-200">
                <i class="fa-solid fa-folder text-indigo-500 mr-1"></i> <?php echo htmlspecialchars($categoryName); ?>
            </span>
            <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg border border-emerald-200">
                <i class="fa-solid fa-book text-emerald-500 mr-1"></i> <?php echo htmlspecialchars($subjectName); ?>
            </span>
        </div>
    </div>

    <!-- Exam Timer & Progress Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6 flex items-center justify-between">
        <div class="text-slate-700 font-bold text-sm">
            <span class="bg-indigo-50 text-indigo-600 font-black px-2.5 py-1 rounded-lg border border-indigo-100 mr-2">
                <?php echo $number; ?>/<?php echo $totalQues; ?>
            </span>
            <span>প্রশ্ন নম্বর <?php echo $number; ?></span>
        </div>

        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5">
            <i class="fa-solid fa-clock text-amber-600"></i>
            <span>অবশিষ্ট সময়: <span id="timer">00:00</span></span>
        </div>
    </div>

    <!-- Question Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 md:p-8">
        <h2 class="text-lg md:text-xl font-bold text-slate-800 mb-6">
            Q<?php echo $number; ?>. <?php echo htmlspecialchars($question['ques'] ?? ''); ?>
        </h2>

        <!-- Form for Submitting Answers -->
        <form id="examForm" action="" method="POST" class="space-y-3">
            <?php
                if (method_exists($exm, 'getAnswers')) {
                    // $quesId (Primary Key: 68) পাস করা হচ্ছে, ফলে অপশনগুলো ডাটাবেজ থেকে চলে আসবে
                    $ans = $exm->getAnswers($quesId);
                    if ($ans):
                        while ($row = $ans->fetch_assoc()):
            ?>
                <label class="flex items-center p-4 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/30 transition cursor-pointer group">
                    <input type="radio" name="ans" value="<?php echo $row['id']; ?>" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300" required>
                    <span class="ml-3 text-sm font-semibold text-slate-700 group-hover:text-slate-900">
                        <?php echo htmlspecialchars($row['ans']); ?>
                    </span>
                </label>
            <?php 
                        endwhile; 
                    endif;
                }
            ?>

            <input type="hidden" name="number" value="<?php echo $number; ?>">
            
            <button type="submit" name="submit" class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 text-sm">
                <span><?php echo ($number == $totalQues) ? 'পরীক্ষা শেষ করুন' : 'পরবর্তী প্রশ্ন'; ?></span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

<!-- JavaScript Auto-Submit Timer -->
<script>
    let remainingTime = <?php echo $remainingSeconds; ?>;

    function updateTimer() {
        let minutes = Math.floor(remainingTime / 60);
        let seconds = remainingTime % 60;

        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        document.getElementById('timer').innerText = minutes + ':' + seconds;

        if (remainingTime <= 0) {
            const requiredInputs = document.querySelectorAll('#examForm [required]');
            requiredInputs.forEach(input => input.removeAttribute('required'));

            const form = document.getElementById('examForm');
            const submitInput = document.createElement('input');
            submitInput.type = 'hidden';
            submitInput.name = 'submit';
            submitInput.value = '1';
            form.appendChild(submitInput);

            form.submit();
        } else {
            remainingTime--;
        }
    }

    updateTimer();
    setInterval(updateTimer, 1000);
</script>

<?php include 'inc/footer.php'; ?>