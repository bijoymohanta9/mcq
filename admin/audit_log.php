<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/inc/header.php');

$roleFilter   = strtolower(trim((string)($_GET['role'] ?? 'all')));
if (!in_array($roleFilter, array('all', 'admin', 'co-admin', 'subscriber'), true)) {
    $roleFilter = 'all';
}

$actionFilter = strtolower(trim((string)($_GET['action_type'] ?? 'all')));
$allowedActions = array(
    'all', 'login', 'logout', 'profile_update', 'password_change',
    'create', 'update', 'delete',
    'sub_request', 'sub_approved', 'sub_rejected', 'sub_pending'
);
if (!in_array($actionFilter, $allowedActions, true)) {
    $actionFilter = 'all';
}

$search = trim((string)($_GET['search'] ?? ''));

$allowedPerPage = array(15, 30, 50);
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 15;
if (!in_array($perPage, $allowedPerPage, true)) {
    $perPage = 15;
}

$whereParts = array("1=1");
if ($roleFilter !== 'all') {
    $safeRoleFilter = mysqli_real_escape_string($db->link, $roleFilter);
    $whereParts[] = "adminRole = '$safeRoleFilter'";
}
if ($actionFilter !== 'all') {
    $safeActionFilter = mysqli_real_escape_string($db->link, $actionFilter);
    $whereParts[] = "action_type = '$safeActionFilter'";
}
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($db->link, $search);
    $whereParts[] = "(adminUser LIKE '%$safeSearch%' OR description LIKE '%$safeSearch%' OR page_name LIKE '%$safeSearch%' OR ip_address LIKE '%$safeSearch%')";
}

$whereSql = implode(' AND ', $whereParts);

$totalLogs = 0;
$countRes = $db->select("SELECT COUNT(*) AS total FROM tbl_audit_log WHERE $whereSql");
if ($countRes && $countRes->num_rows > 0) {
    $countRow = $countRes->fetch_assoc();
    $totalLogs = (int)($countRow['total'] ?? 0);
}

$totalPages = max(1, (int)ceil($totalLogs / $perPage));
$pageNo     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pageNo     = min($pageNo, $totalPages);
$offset     = ($pageNo - 1) * $perPage;

$logsResult = $db->select("SELECT * FROM tbl_audit_log WHERE $whereSql ORDER BY id DESC LIMIT $perPage OFFSET $offset");

$queryParams = array(
    'role'        => $roleFilter,
    'action_type' => $actionFilter,
    'search'      => $search,
    'per_page'    => $perPage
);

$fromRecord = $totalLogs > 0 ? $offset + 1 : 0;
$toRecord   = min($offset + $perPage, $totalLogs);

$actionLabels = array(
    'login'           => array('লগইন', 'bg-emerald-50 text-emerald-700 border-emerald-200'),
    'logout'          => array('লগআউট', 'bg-slate-100 text-slate-700 border-slate-200'),
    'profile_update'  => array('প্রোফাইল আপডেট', 'bg-indigo-50 text-indigo-700 border-indigo-200'),
    'password_change' => array('পাসওয়ার্ড পরিবর্তন', 'bg-amber-50 text-amber-700 border-amber-200'),
    'create'          => array('তৈরি', 'bg-sky-50 text-sky-700 border-sky-200'),
    'update'          => array('আপডেট', 'bg-violet-50 text-violet-700 border-violet-200'),
    'delete'          => array('ডিলিট', 'bg-rose-50 text-rose-700 border-rose-200'),
    'sub_request'     => array('সাবস্ক্রিপশন রিকোয়েস্ট', 'bg-teal-50 text-teal-700 border-teal-200'),
    'sub_approved'    => array('সাবস্ক্রিপশন Approved', 'bg-emerald-50 text-emerald-700 border-emerald-200'),
    'sub_rejected'    => array('সাবস্ক্রিপশন Rejected', 'bg-rose-50 text-rose-700 border-rose-200'),
    'sub_pending'     => array('সাবস্ক্রিপশন Pending', 'bg-amber-50 text-amber-700 border-amber-200'),
);
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                <span>Audit Log (সকল ইউজার ও সাবস্ক্রিপশন)</span>
            </h1>
            <p class="text-slate-500 text-xs mt-1">অ্যাডমিন, কো-অ্যাডমিন এবং সাবস্ক্রিপশন করা ইউজারদের কার্যক্রমের ইতিহাস</p>
        </div>
        <a href="index.php" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left"></i><span>ড্যাশবোর্ডে ফিরুন</span>
        </a>
    </div>

    <!-- Filter Panel -->
    <form method="GET" action="audit_log.php" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label for="role" class="block text-xs font-bold text-slate-600 mb-2">ইউজার রোল</label>
                <select name="role" id="role" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="all" <?php echo $roleFilter === 'all' ? 'selected' : ''; ?>>সকল ইউজার (All)</option>
                    <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="co-admin" <?php echo $roleFilter === 'co-admin' ? 'selected' : ''; ?>>Co-Admin</option>
                    <option value="subscriber" <?php echo $roleFilter === 'subscriber' ? 'selected' : ''; ?>>Subscriber (ইউজার সাবস্ক্রিপশন)</option>
                </select>
            </div>

            <div>
                <label for="action_type" class="block text-xs font-bold text-slate-600 mb-2">অ্যাকশন টাইপ</label>
                <select name="action_type" id="action_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="all" <?php echo $actionFilter === 'all' ? 'selected' : ''; ?>>সকল অ্যাকশন</option>
                    <?php foreach ($actionLabels as $actKey => $actMeta): ?>
                        <option value="<?php echo htmlspecialchars($actKey, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $actionFilter === $actKey ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($actMeta[0], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="search" class="block text-xs font-bold text-slate-600 mb-2">খুঁজুন (ইউজার / বিবরণ / IP)</label>
                <input type="search" name="search" id="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="নাম, ইমেইল, TrxID বা IP..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div>
                <label for="per_page" class="block text-xs font-bold text-slate-600 mb-2">প্রতি পেজে দেখান</label>
                <select name="per_page" id="per_page" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <?php foreach ($allowedPerPage as $n): ?>
                        <option value="<?php echo $n; ?>" <?php echo $perPage === $n ? 'selected' : ''; ?>><?php echo $n; ?> টি</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 mt-4">
            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-3 py-1">
                মোট অডিট লগ: <?php echo number_format($totalLogs); ?> টি
            </span>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    <i class="fa-solid fa-magnifying-glass"></i> ফিল্টার করুন
                </button>
                <a href="audit_log.php" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-rotate-left"></i> রিসেট
                </a>
            </div>
        </div>
    </form>

    <!-- Audit Table Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-extrabold text-slate-800">অ্যাক্টিভিটি লগ তালিকা</h2>
                <p class="text-[11px] text-slate-400 mt-1">সর্বশেষ কার্যক্রমের ক্রমানুসারে সাজানো</p>
            </div>
            <p class="text-xs text-slate-500">
                দেখানো হচ্ছে <strong class="text-slate-800"><?php echo $fromRecord; ?>-<?php echo $toRecord; ?></strong>
                / <strong class="text-slate-800"><?php echo $totalLogs; ?></strong>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-14 text-center">#</th>
                        <th class="px-4 py-3">ইউজার ও রোল</th>
                        <th class="px-4 py-3 text-center">অ্যাকশন</th>
                        <th class="px-4 py-3">বিবরণ</th>
                        <th class="px-4 py-3">পেজ</th>
                        <th class="px-4 py-3">IP ঠিকানা</th>
                        <th class="px-4 py-3">সময়</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($logsResult && $logsResult->num_rows > 0): ?>
                        <?php $serial = $offset; while ($row = $logsResult->fetch_assoc()): $serial++; ?>
                            <?php
                                $actKey   = strtolower((string)($row['action_type'] ?? 'info'));
                                $actTitle = $actionLabels[$actKey][0] ?? ucfirst($actKey);
                                $actBadge = $actionLabels[$actKey][1] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                $rowRole  = strtolower((string)($row['adminRole'] ?? 'admin'));
                                $created  = !empty($row['created_at']) ? strtotime((string)$row['created_at']) : false;
                            ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 text-center font-bold text-slate-400"><?php echo $serial; ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800"><?php echo htmlspecialchars((string)($row['adminUser'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase <?php
                                        if ($rowRole === 'co-admin') {
                                            echo 'bg-amber-100 text-amber-800';
                                        } elseif ($rowRole === 'subscriber') {
                                            echo 'bg-teal-100 text-teal-800';
                                        } else {
                                            echo 'bg-indigo-100 text-indigo-800';
                                        }
                                    ?>">
                                        <?php echo htmlspecialchars($rowRole, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold border <?php echo $actBadge; ?>">
                                        <?php echo htmlspecialchars($actTitle, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-700">
                                    <?php echo htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-4 py-3 text-slate-500 font-mono text-[11px]">
                                    <?php echo htmlspecialchars((string)($row['page_name'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-4 py-3 text-slate-500 font-mono text-[11px]">
                                    <?php echo htmlspecialchars((string)($row['ip_address'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                    <?php echo $created ? date("d M, Y h:i A", $created) : '-'; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-400">কোনো অডিট লগ পাওয়া যায়নি।</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalLogs > 0 && $totalPages > 1): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                <p class="text-xs text-slate-500">পৃষ্ঠা <strong class="text-slate-800"><?php echo $pageNo; ?></strong> / <?php echo $totalPages; ?></p>
                <div class="flex flex-wrap items-center justify-center gap-1.5">
                    <?php
                        $prev = max(1, $pageNo - 1);
                        $next = min($totalPages, $pageNo + 1);
                    ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $prev))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo $pageNo === 1 ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>">পূর্ববর্তী</a>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $p))), ENT_QUOTES, 'UTF-8'); ?>" class="min-w-9 px-3 py-2 text-center rounded-lg text-xs font-bold <?php echo $p === $pageNo ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>"><?php echo $p; ?></a>
                    <?php endfor; ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $next))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo $pageNo === $totalPages ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>">পরবর্তী</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'inc/footer.php'; ?>
