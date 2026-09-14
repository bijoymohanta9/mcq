<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');

    // ইউজার সেশন চেক
    Session::checkSession();
    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");

    $exm = new Exam();
    $msg = "";

    // ১. পেমেন্ট সাবমিট প্রসেস করা
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
        $msg = $exm->createSubscriptionRequest($_POST, $userId);
    }

    // ২. GET Parameter থেকে ডাটা গ্রহণ করা
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 1;
    $rawPlan    = isset($_GET['plan']) ? trim($_GET['plan']) : '6_months';
    $amount     = isset($_GET['amount']) ? (int)$_GET['amount'] : 900;

    // ৩. ক্যাটাগরির নাম ফেচ করা (getCategoryById মেথড না থাকলেও safe fallback কাজ করবে)
    $categoryName = "Selected Exam";
    if (method_exists($exm, 'getCategoryById')) {
        $getCat = $exm->getCategoryById($categoryId);
        if ($getCat && $catRow = $getCat->fetch_assoc()) {
            $categoryName = $catRow['category_name'];
        }
    } else {
        $getCats = $exm->getCategories();
        if ($getCats) {
            while ($catRow = $getCats->fetch_assoc()) {
                if ($catRow['id'] == $categoryId) {
                    $categoryName = $catRow['category_name'];
                    break;
                }
            }
        }
    }

    // ৪. মেয়াদের নাম সুন্দরভাবে দেখানোর জন্য ম্যাপিং
    $planMap = [
        '3_months' => '৩ মাস (90 Days)',
        '6_months' => '৬ মাস (180 Days)',
        '1_year'   => '১ বছর (365 Days)',
        '30 Days'  => '৩০ দিন',
        '90 Days'  => '৯০ দিন',
        '365 Days' => '৩৬৫ দিন'
    ];

    $displayDuration = isset($planMap[$rawPlan]) ? $planMap[$rawPlan] : $rawPlan;
?>

<div class="w-full max-w-lg mx-auto my-10 px-4">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-slate-800 mb-1">সাবস্ক্রিপশন পেমেন্ট</h2>
        <p class="text-xs text-slate-500 mb-6">নিচের নম্বরে সেন্ড মানি করে ট্রানজেকশন আইডি দিন</p>

        <?php if(!empty($msg)) echo $msg; ?>

        <!-- Merchant/Personal Numbers -->
        <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100 text-xs space-y-2">
            <div class="flex justify-between items-center">
                <span class="font-bold text-pink-600">bKash (Personal):</span>
                <span class="font-mono font-bold text-slate-700">01717644512</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-orange-600">Nagad (Personal):</span>
                <span class="font-mono font-bold text-slate-700">01302650999</span>
            </div>
        </div>

        <form action="" method="POST" class="space-y-4 text-xs">
            
            <!-- Dynamic Exam Category -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">পরীক্ষার ধরন / প্যাকেজ</label>
                <input type="text" value="<?php echo htmlspecialchars($categoryName); ?>" class="w-full p-2.5 border rounded-lg bg-slate-50 font-semibold" readonly>
                <input type="hidden" name="exam_type" value="<?php echo htmlspecialchars($categoryName); ?>">
                <input type="hidden" name="category_id" value="<?php echo $categoryId; ?>">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Duration -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">মেয়াদ</label>
                    <input type="text" value="<?php echo htmlspecialchars($displayDuration); ?>" class="w-full p-2.5 border rounded-lg bg-slate-50 font-semibold" readonly>
                    <input type="hidden" name="duration" value="<?php echo htmlspecialchars($displayDuration); ?>">
                </div>
                <!-- Amount -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">পরিমাণ (BDT)</label>
                    <input type="number" name="amount" value="<?php echo $amount; ?>" class="w-full p-2.5 border rounded-lg bg-slate-50 font-bold" readonly>
                </div>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">পেমেন্ট মেথড</label>
                <select name="payment_method" class="w-full p-2.5 border rounded-lg focus:ring-1 focus:ring-indigo-500" required>
                    <option value="bKash">bKash</option>
                    <option value="Nagad">Nagad</option>
                    <option value="Rocket">Rocket</option>
                </select>
            </div>

            <!-- Sender Number -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">যে নম্বর থেকে পাঠিয়েছেন</label>
                <input type="text" name="sender_number" placeholder="017XXXXXXXX" class="w-full p-2.5 border rounded-lg" required>
            </div>

            <!-- Transaction ID -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Transaction ID (TrxID)</label>
                <input type="text" name="trx_id" placeholder="e.g. 9J87AKL0P" class="w-full p-2.5 border rounded-lg uppercase font-mono" required>
            </div>

            <button type="submit" name="submit_payment" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition">
                পেমেন্ট নিশ্চিত করুন
            </button>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>