<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');

    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");
    $exm = new Exam();

    $msg = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
        $msg = $exm->createSubscriptionRequest($_POST, $userId);
    }
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
            <!-- Exam / Category Selection -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">পরীক্ষার ধরন / প্যাকেজ</label>
                <input type="text" name="exam_type" value="Premium Subscription" class="w-full p-2.5 border rounded-lg bg-slate-50 font-semibold" readonly>
                <input type="hidden" name="category_id" value="1"> <!-- Dynamic ID -->
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">মেয়াদ</label>
                    <select name="duration" class="w-full p-2.5 border rounded-lg focus:ring-1 focus:ring-indigo-500">
                        <option value="30 Days">30 Days</option>
                        <option value="90 Days">90 Days</option>
                        <option value="365 Days">365 Days</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">পরিমাণ (BDT)</label>
                    <input type="number" name="amount" value="500" class="w-full p-2.5 border rounded-lg font-bold" readonly>
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

            <div>
                <label class="block font-bold text-slate-700 mb-1">যে নম্বর থেকে পাঠিয়েছেন</label>
                <input type="text" name="sender_number" placeholder="017XXXXXXXX" class="w-full p-2.5 border rounded-lg" required>
            </div>

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