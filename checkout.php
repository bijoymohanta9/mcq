<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');

    // Ensure session security & user identifier retrieval
    Session::checkSession();
    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");

    $exm = new Exam();
    $msg = "";

    // Process payment submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
        $msg = $exm->createSubscriptionRequest($_POST, $userId);
    }

    // Capture GET parameters passed from subscription.php
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 1;
    $rawPlan    = isset($_GET['plan']) ? $_GET['plan'] : '6_months';
    $amount     = isset($_GET['amount']) ? (int)$_GET['amount'] : 900;

    // Fetch dynamic category details
    $categoryName = "Standard Package";
    $getCat = $exm->getCategoryById($categoryId); // Ensure your Exam class handles fetching single category details, or fallback to query
    if ($getCat && $catRow = $getCat->fetch_assoc()) {
        $categoryName = $catRow['category_name'];
    }

    // Map internal plan codes to readable string formats
    $durationMap = [
        '3_months' => '3 Months (90 Days)',
        '6_months' => '6 Months (180 Days)',
        '1_year'   => '1 Year (365 Days)'
    ];
    $displayDuration = isset($durationMap[$rawPlan]) ? $durationMap[$rawPlan] : $rawPlan;
?>

<div class="w-full max-w-xl mx-auto my-10 px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
        
        <!-- Header Banner -->
        <div class="bg-slate-50 border-b border-slate-100 p-6 text-center">
            <h2 class="text-2xl font-bold text-slate-800">সাবস্ক্রিপশন পেমেন্ট</h2>
            <p class="text-xs text-slate-500 mt-1">নিচের নম্বর সেন্ড মানি করে ট্রানজেকশন আইডি প্রদান করুন</p>
        </div>

        <form id="checkoutForm" class="p-6 md:p-8 space-y-5">
            
            <!-- Merchant Payment Accounts Box -->
            <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-pink-600">bKash (Personal):</span>
                    <span class="font-mono font-bold text-slate-700 text-sm select-all">01717644512</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-orange-600">Nagad (Personal):</span>
                    <span class="font-mono font-bold text-slate-700 text-sm select-all">01302650999</span>
                </div>
            </div>

            <!-- Read-only Selected Package Details -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">পরীক্ষার ধরন / প্যাকেজ</label>
                <input type="text" value="<?php echo htmlspecialchars($categoryName); ?>" readonly class="w-full bg-slate-100 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none cursor-not-allowed">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">মেয়াদ</label>
                    <input type="text" value="<?php echo htmlspecialchars($planLabel); ?>" readonly class="w-full bg-slate-100 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">পরিমাণ (BDT)</label>
                    <input type="text" value="৳ <?php echo htmlspecialchars($amount); ?>" readonly class="w-full bg-slate-100 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-bold text-indigo-600 focus:outline-none cursor-not-allowed">
                </div>
            </div>

            <!-- Payment Form Inputs -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">পেমেন্ট মেথড <span class="text-rose-500">*</span></label>
                <select name="payment_method" required class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="bKash">bKash</option>
                    <option value="Nagad">Nagad</option>
                    <option value="Rocket">Rocket</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">যে নম্বর থেকে পাঠিয়েছেন <span class="text-rose-500">*</span></label>
                <input type="text" name="sender_number" placeholder="017XXXXXXXX" required class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Transaction ID (TrxID) <span class="text-rose-500">*</span></label>
                <input type="text" name="transaction_id" placeholder="E.G. 9J87AKL0P" required class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm font-mono text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <!-- Hidden Submit Inputs -->
            <input type="hidden" name="category_id" value="<?php echo $categoryId; ?>">
            <input type="hidden" name="plan" value="<?php echo $plan; ?>">
            <input type="hidden" name="amount" value="<?php echo $amount; ?>">

            <!-- Submit Button -->
            <button type="submit" id="submitPaymentBtn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center space-x-2 text-base mt-2">
                <span>পেমেন্ট নিশ্চিত করুন</span>
                <i class="fa-solid fa-check-circle text-sm"></i>
            </button>
        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>