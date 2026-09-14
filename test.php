<?php include 'inc/header.php'; ?>
<?php
Session::checkSession();

// সেশন থেকে কাস্টম পরীক্ষার ডাটা ফেচ করা
$examQuestions = Session::get("exam_questions");
$total         = Session::get("exam_total_ques");
$timeLimit     = Session::get("exam_time_limit"); // মিনিটে
$startTime     = Session::get("exam_start_time");

// যদি সেশনে প্রশ্ন না থাকে তবে রিডাইরেক্ট করবে
if (!$examQuestions || empty($examQuestions)) {
    header("Location: starttest.php");
    exit();
}

// বর্তমান প্রশ্ন সূচক (১, ২, ৩...)
$step = isset($_GET['q']) ? (int)$_GET['q'] : 1;

if ($step < 1) {
    $step = 1;
} elseif ($step > $total) {
    header("Location: final.php");
    exit();
}

// বর্তমান স্টেপের আসল প্রশ্ন নম্বর বের করা
$quesNo = $examQuestions[$step - 1];

// প্রশ্ন ও উত্তর ডাটাবেজ থেকে নিয়ে আসা
$question = $exm->getQuesByNumber($quesNo);
$answer   = $exm->getAnswer($quesNo);

// ফর্ম সাবমিট হ্যান্ডলিং
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['number'] = $quesNo; // মূল প্রশ্ন নম্বর প্রসেসে পাঠানো
    $process = $pro->processData($_POST, $step, $total);
}

// সময় গণনার লজিক (Seconds)
$elapsedTime = time() - $startTime;
$totalSeconds = $timeLimit * 60;
$remainingSeconds = max(0, $totalSeconds - $elapsedTime);
?>

<div class="max-w-3xl mx-auto my-8 px-4">
    
    <!-- Top Bar: Progress & Timer -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Progress Info -->
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 font-black text-sm flex items-center justify-center border border-indigo-100">
                <?php echo $step; ?>/<?php echo $total; ?>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">প্রশ্ন নম্বর <?php echo $step; ?></h2>
                <p class="text-xs text-slate-500">মোট <?php echo $total; ?> টি প্রশ্নের মধ্যে</p>
            </div>
        </div>

        <!-- Timer Display -->
        <div class="flex items-center gap-2 bg-slate-900 text-amber-400 px-4 py-2 rounded-xl text-sm font-bold shadow-inner">
            <i class="fa-solid fa-clock text-amber-400 animate-pulse"></i>
            <span>অবশিষ্ট সময়: <span id="timer">--:--</span></span>
        </div>
    </div>

    <!-- Question Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        
        <!-- Question Title Header -->
        <div class="bg-slate-50 border-b border-slate-100 p-6">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 leading-relaxed">
                <span class="text-indigo-600 font-black mr-1">Q<?php echo $step; ?>.</span> 
                <?php echo htmlspecialchars($question['ques']); ?>
            </h3>
        </div>

        <!-- Options Form -->
        <form method="post" action="" class="p-6">
            <div class="space-y-3 mb-8">
                <?php 
                    if ($answer) {
                        while ($result = $answer->fetch_assoc()) {
                ?>
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/30 cursor-pointer transition group">
                        <input type="radio" name="ans" value="<?php echo $result['id']; ?>" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300" required />
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-slate-900">
                            <?php echo htmlspecialchars($result['ans']); ?>
                        </span>
                    </label>
                <?php 
                        }
                    } 
                ?>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <input type="hidden" name="number" value="<?php echo $quesNo; ?>" />
                <button type="submit" name="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center gap-2 text-sm">
                    <span><?php echo ($step == $total) ? 'পরীক্ষা শেষ করুন' : 'পরবর্তী প্রশ্ন'; ?></span>
                    <i class="fa-solid <?php echo ($step == $total) ? 'fa-check-circle' : 'fa-arrow-right'; ?>"></i>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Countdown Timer Script -->
<script>
    let timeLeft = <?php echo $remainingSeconds; ?>;
    const timerDisplay = document.getElementById('timer');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        timerDisplay.textContent = 
            `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (timeLeft <= 0) {
            alert('আপনার নির্ধারিত সময় শেষ হয়ে গেছে!');
            window.location.href = 'final.php';
        } else {
            timeLeft--;
        }
    }

    updateTimer();
    setInterval(updateTimer, 1000);
</script>

<?php include 'inc/footer.php'; ?>