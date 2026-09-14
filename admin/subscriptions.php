<?php 
    include 'inc/header.php';
    include '../classes/Exam.php';
    $exm = new Exam();

    // Handle Status Update Action
    $msg = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
        $sub_id   = $_POST['sub_id'];
        $status   = $_POST['status'];
        $duration = $_POST['duration'];
        $msg = $exm->updateSubscriptionStatus($sub_id, $status, $duration);
    }
?>

<div class="w-full max-w-7xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Manage Subscriptions</h2>
    
    <?php if(!empty($msg)) echo $msg; ?>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="p-4">User</th>
                        <th class="p-4">Package</th>
                        <th class="p-4">Duration</th>
                        <th class="p-4">Method / TrxID</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                        $getSubs = $exm->getAllSubscriptions();
                        if ($getSubs) {
                            while ($row = $getSubs->fetch_assoc()) {
                                $currentStatus = strtolower(trim($row['status']));
                    ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        <!-- User Info -->
                        <td class="p-4">
                            <span class="font-bold text-slate-900 block"><?php echo htmlspecialchars($row['user_name']); ?></span>
                            <span class="text-xs text-slate-500 font-mono"><?php echo htmlspecialchars($row['email']); ?></span>
                        </td>
                        
                        <!-- Package -->
                        <td class="p-4 font-semibold text-slate-800">
                            <?php echo htmlspecialchars($row['exam_type']); ?>
                        </td>
                        
                        <!-- Duration -->
                        <td class="p-4">
                            <?php echo htmlspecialchars($row['duration']); ?>
                        </td>
                        
                        <!-- Payment Details -->
                        <td class="p-4">
                            <span class="font-bold text-slate-800"><?php echo htmlspecialchars($row['payment_method']); ?></span>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">
                                Trx: <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($row['trx_id']); ?></span>
                                (<?php echo htmlspecialchars($row['sender_number']); ?>)
                            </div>
                        </td>
                        
                        <!-- Amount -->
                        <td class="p-4 font-bold text-slate-900">
                            ৳<?php echo number_format($row['amount'], 2); ?>
                        </td>
                        
                        <!-- Status Badge -->
                        <td class="p-4 text-center">
                            <?php if ($currentStatus == 'approved'): ?>
                                <span class="px-3 py-1 text-xs rounded-full font-bold bg-green-100 text-green-700 border border-green-200 inline-block">
                                    Approved
                                </span>
                            <?php elseif ($currentStatus == 'rejected'): ?>
                                <span class="px-3 py-1 text-xs rounded-full font-bold bg-red-100 text-red-700 border border-red-200 inline-block">
                                    Rejected
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 text-xs rounded-full font-bold bg-amber-100 text-amber-800 border border-amber-200 inline-block">
                                    Pending
                                </span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Action Form -->
                        <td class="p-4 text-center">
                            <form action="" method="POST" class="flex items-center justify-center gap-2">
                                <input type="hidden" name="sub_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="duration" value="<?php echo htmlspecialchars($row['duration']); ?>">
                                
                                <select name="status" class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none bg-white">
                                    <option value="pending" <?php if($currentStatus=='pending') echo 'selected'; ?>>Pending</option>
                                    <option value="approved" <?php if($currentStatus=='approved') echo 'selected'; ?>>Approved</option>
                                    <option value="rejected" <?php if($currentStatus=='rejected') echo 'selected'; ?>>Rejected</option>
                                </select>
                                
                                <button type="submit" name="update_status" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-lg transition text-xs shadow-sm">
                                    Save
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } } else { ?>
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500">No subscriptions found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>