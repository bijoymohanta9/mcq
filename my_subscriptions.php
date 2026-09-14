<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');

    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");
    
    // Check if user is logged in
    Session::checkSession();
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        
        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <div>
                <h2 class="text-xl font-bold text-slate-800">আমার সাবস্ক্রিপশনসমূহ</h2>
                <p class="text-xs text-slate-500 mt-1">আপনার কেনা সমস্ত প্যাকেজের তালিকা ও বর্তমান স্ট্যাটাস</p>
            </div>
            <a href="checkout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> নতুন প্ল্যান কিনুন
            </a>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="p-4">প্যাকেজ / টাইপ</th>
                        <th class="p-4">মেয়াদ</th>
                        <th class="p-4">মেথড / TrxID</th>
                        <th class="p-4">পরিমাণ</th>
                        <th class="p-4">স্ট্যাটাস</th>
                        <th class="p-4">মেয়াদ শেষ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <?php 
                        $getUserSub = $exm->getSubscriptionByUserId($userId);
                        if ($getUserSub && $getUserSub->num_rows > 0) {
                            while ($data = $getUserSub->fetch_assoc()) {
                                $status      = strtolower(trim((string)($data['status'] ?? '')));
                                $currentDate = date('Y-m-d H:i:s');
                                $expireDate  = $data['expire_date'] ?? '';
                    ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-bold text-slate-900">
                                <?php echo htmlspecialchars($data['exam_type'] ?? 'General Exam'); ?>
                            </td>
                            <td class="p-4">
                                <?php echo htmlspecialchars($data['duration'] ?? ''); ?>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-slate-800">
                                    <?php echo !empty($data['payment_method']) ? htmlspecialchars($data['payment_method']) : 'N/A'; ?>
                                </span>
                                <span class="block text-[10px] text-slate-400 font-mono">
                                    <?php echo !empty($data['trx_id']) ? htmlspecialchars($data['trx_id']) : ''; ?>
                                </span>
                            </td>
                            <td class="p-4 font-bold">
                                ৳<?php echo number_format((float)($data['amount'] ?? 0), 2); ?>
                            </td>
                            <td class="p-4">
                                <?php 
                                    if ($status == 'rejected') {
                                        echo '<span class="px-2.5 py-1 bg-rose-100 text-rose-700 font-bold rounded-full text-[10px] inline-flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>';
                                    } elseif ($status == 'approved' && !empty($expireDate) && $currentDate > $expireDate) {
                                        echo '<span class="px-2.5 py-1 bg-rose-100 text-rose-700 font-bold rounded-full text-[10px] inline-flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i> Expired</span>';
                                    } elseif ($status == 'approved') {
                                        echo '<span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-bold rounded-full text-[10px] inline-flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Active</span>';
                                    } else {
                                        echo '<span class="px-2.5 py-1 bg-amber-100 text-amber-700 font-bold rounded-full text-[10px] inline-flex items-center gap-1"><i class="fa-solid fa-clock"></i> Pending</span>';
                                    }
                                ?>
                            </td>
                            <td class="p-4 text-slate-500 font-mono">
                                <?php echo (!empty($expireDate) && $expireDate != '0000-00-00 00:00:00' && $status == 'approved') ? date('d M, Y', strtotime($expireDate)) : 'N/A'; ?>
                            </td>
                        </tr>
                    <?php 
                            }
                        } else {
                    ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                                আপনি এখনও কোনো সাবস্ক্রিপশন কেনেননি।
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include 'inc/footer.php'; ?>