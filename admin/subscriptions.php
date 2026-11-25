<?php 
    include 'inc/header.php';
    include '../classes/Exam.php';
    $exm = new Exam();

    $currentRole = strtolower(trim($_SESSION['adminRole'] ?? $_SESSION['adminUser'] ?? 'admin'));
    $isCoAdmin = ($currentRole === 'co-admin');

    // Handle Status Update Action (শুধুমাত্র admin এর জন্য)
    $msg = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
        if ($isCoAdmin) {
            $msg = "<div class='p-3 mb-4 text-sm text-rose-800 rounded-lg bg-rose-50 border border-rose-200 font-bold'>Co-Admin এর Subscription Status পরিবর্তন করার অনুমতি নেই।</div>";
        } else {
            $sub_id   = $_POST['sub_id'];
            $status   = $_POST['status'];
            $duration = $_POST['duration'];
            $msg = $exm->updateSubscriptionStatus($sub_id, $status, $duration);
        }
    }

    // Filter Inputs (GET)
    $search        = isset($_GET['search']) ? trim($_GET['search']) : '';
    $statusFilter  = isset($_GET['filter_status']) ? strtolower(trim($_GET['filter_status'])) : '';
    $methodFilter  = isset($_GET['filter_method']) ? strtolower(trim($_GET['filter_method'])) : '';
    $packageFilter = isset($_GET['filter_package']) ? trim($_GET['filter_package']) : '';
    $fromDate      = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
    $toDate        = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';

    // Pagination Setup
    $perPage     = 10;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $totalRows   = $exm->getFilteredSubscriptionsCount($search, $statusFilter, $methodFilter, $packageFilter, $fromDate, $toDate);
    $totalPages  = max(1, (int)ceil($totalRows / $perPage));
    if ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    }
    $offset = ($currentPage - 1) * $perPage;

    // Helper for keeping query params in tabs & pagination links
    function buildSubQueryUrl($overrides = []) {
        $params = [
            'search'         => $_GET['search'] ?? '',
            'filter_status'  => $_GET['filter_status'] ?? '',
            'filter_method'  => $_GET['filter_method'] ?? '',
            'filter_package' => $_GET['filter_package'] ?? '',
            'from_date'      => $_GET['from_date'] ?? '',
            'to_date'        => $_GET['to_date'] ?? '',
            'page'           => $_GET['page'] ?? 1,
        ];
        foreach ($overrides as $k => $v) {
            $params[$k] = $v;
        }
        $clean = array_filter($params, function($val) {
            return $val !== '' && $val !== null;
        });
        $qs = http_build_query($clean);
        return 'subscriptions.php' . ($qs ? ('?' . $qs) : '');
    }

    $packagesRes = $exm->getSubscriptionPackages();
?>

<div class="w-full max-w-7xl mx-auto py-8 px-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <h2 class="text-2xl font-bold text-slate-800">Manage Subscriptions</h2>
        <span class="text-xs font-semibold text-slate-500">
            Total Results: <strong class="text-slate-800"><?php echo $totalRows; ?></strong>
        </span>
    </div>
    
    <?php if(!empty($msg)) echo $msg; ?>

    <!-- Quick Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <?php
            $tabs = [
                ''         => 'All',
                'pending'  => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ];
            foreach ($tabs as $tabVal => $tabLabel):
                $isActiveTab = ($statusFilter === $tabVal);
                $tabUrl = buildSubQueryUrl(['filter_status' => $tabVal, 'page' => 1]);
        ?>
            <a href="<?php echo htmlspecialchars($tabUrl); ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition <?php echo $isActiveTab ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'; ?>">
                <?php echo htmlspecialchars($tabLabel); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="subscriptions.php" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Search (User / Email / TrxID / Phone)</label>
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="Search name, email, trx_id, number..." 
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none"
                >
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="filter_status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none">
                    <option value="">All Status</option>
                    <option value="pending" <?php if($statusFilter == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="approved" <?php if($statusFilter == 'approved') echo 'selected'; ?>>Approved</option>
                    <option value="rejected" <?php if($statusFilter == 'rejected') echo 'selected'; ?>>Rejected</option>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Method</label>
                <select name="filter_method" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none">
                    <option value="">All Methods</option>
                    <option value="bkash" <?php if($methodFilter == 'bkash') echo 'selected'; ?>>bKash</option>
                    <option value="nagad" <?php if($methodFilter == 'nagad') echo 'selected'; ?>>Nagad</option>
                    <option value="rocket" <?php if($methodFilter == 'rocket') echo 'selected'; ?>>Rocket</option>
                </select>
            </div>

            <!-- Package Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Package</label>
                <select name="filter_package" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs sm:text-sm bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none">
                    <option value="">All Packages</option>
                    <?php if ($packagesRes && $packagesRes->num_rows > 0): ?>
                        <?php while ($pkgRow = $packagesRes->fetch_assoc()): ?>
                            <?php $pkgName = $pkgRow['exam_type']; ?>
                            <option value="<?php echo htmlspecialchars($pkgName); ?>" <?php if($packageFilter === $pkgName) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($pkgName); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Date Range (From & To) -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">From</label>
                    <input 
                        type="date" 
                        name="from_date" 
                        value="<?php echo htmlspecialchars($fromDate); ?>" 
                        class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none"
                    >
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">To</label>
                    <input 
                        type="date" 
                        name="to_date" 
                        value="<?php echo htmlspecialchars($toDate); ?>" 
                        class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none"
                    >
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 mt-4">
            <a href="subscriptions.php" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold transition shadow-sm">
                Apply Filter
            </button>
        </div>
    </form>

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
                        $getSubs = $exm->getFilteredSubscriptionsPaginated(
                            $search, $statusFilter, $methodFilter, $packageFilter, $fromDate, $toDate, $perPage, $offset
                        );
                        if ($getSubs && $getSubs->num_rows > 0) {
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
                            <?php if (!$isCoAdmin): ?>
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
                            <?php else: ?>
                                <span class="text-xs text-slate-400 font-semibold">View Only</span>
                            <?php endif; ?>
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

        <!-- Pagination Bar -->
        <?php if ($totalPages > 1): ?>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 bg-slate-50 border-t border-slate-200 text-xs">
                <div class="text-slate-600 font-medium">
                    Page <strong class="text-slate-900"><?php echo $currentPage; ?></strong> of <strong class="text-slate-900"><?php echo $totalPages; ?></strong>
                </div>

                <div class="flex items-center gap-1 flex-wrap">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?php echo htmlspecialchars(buildSubQueryUrl(['page' => $currentPage - 1])); ?>"
                           class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 font-semibold transition">
                            Prev
                        </a>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="<?php echo htmlspecialchars(buildSubQueryUrl(['page' => $p])); ?>"
                           class="px-3 py-1.5 rounded-lg border font-semibold transition <?php echo ($p === $currentPage) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100'; ?>">
                            <?php echo $p; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?php echo htmlspecialchars(buildSubQueryUrl(['page' => $currentPage + 1])); ?>"
                           class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 font-semibold transition">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer.php'; ?>