<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../classes/Exam.php';
$exm = new Exam();

$currentRole = strtolower(trim($_SESSION['adminRole'] ?? $_SESSION['adminUser'] ?? 'admin'));
$isCoAdmin = ($currentRole === 'co-admin');

// Delete handler: delete করার পর একই filter-এ ফিরে যাবে (শুধুমাত্র admin এর জন্য)
if (isset($_GET['del_id'])) {
    if ($isCoAdmin) {
        $_SESSION['msg'] = "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>Co-Admin এর Delete করার অনুমতি নেই।</div>";
    } else {
        $del_id = (int)$_GET['del_id'];
        $deleteMessage = $exm->deleteCircular($del_id);
        $_SESSION['msg'] = $deleteMessage;
    }

    $returnParams = array(
        'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0,
        'status' => isset($_GET['status']) ? $_GET['status'] : 'all',
        'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
        'per_page' => isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10,
        'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1
    );
    header("Location: circular_list.php?" . http_build_query($returnParams));
    exit();
}

include_once 'inc/header.php';

$msg = '';
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Filter values
$category_id = isset($_GET['category_id']) ? max(0, (int)$_GET['category_id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
if (!in_array($status, array('all', 'active', 'expired'), true)) {
    $status = 'all';
}
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$allowedPerPage = array(10, 20, 50);
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
if (!in_array($per_page, $allowedPerPage, true)) {
    $per_page = 10;
}

$total_circulars = $exm->getCircularListCount($category_id, $status, $search);
$total_pages = max(1, (int)ceil($total_circulars / $per_page));
$pageNo = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pageNo = min($pageNo, $total_pages);
$offset = ($pageNo - 1) * $per_page;

$getAll = $exm->getCircularListPaginated($category_id, $status, $search, $per_page, $offset);
$categories = $exm->getAllCategories();

$queryParams = array(
    'category_id' => $category_id,
    'status' => $status,
    'search' => $search,
    'per_page' => $per_page
);
$fromRecord = $total_circulars > 0 ? $offset + 1 : 0;
$toRecord = min($offset + $per_page, $total_circulars);
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4 space-y-6">

    <!-- Page heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">সার্কুলার তালিকা</h1>
            <p class="text-slate-500 text-xs mt-1">আপনার সিস্টেমে থাকা সকল জব সার্কুলার খুঁজুন ও পরিচালনা করুন</p>
        </div>
        <a href="circular_add.php" class="px-4 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i><span>নতুন সার্কুলার</span>
        </a>
    </div>

    <?php if (!empty($msg)): ?>
        <div><?php echo $msg; ?></div>
    <?php endif; ?>

    <!-- Filter panel -->
    <form method="GET" action="circular_list.php" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-filter text-indigo-600"></i> সার্কুলার ফিল্টার
                </h2>
                <p class="text-[11px] text-slate-400 mt-1">ক্যাটাগরি, মেয়াদ ও শিরোনাম দিয়ে সার্কুলার খুঁজুন</p>
            </div>
            <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 rounded-full px-3 py-1.5 w-fit">
                মোট <?php echo number_format($total_circulars); ?> টি সার্কুলার
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-600 mb-2">ক্যাটাগরি</label>
                <select name="category_id" id="category_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="0">সকল ক্যাটাগরি</option>
                    <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo $category_id === (int)$cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
                <label for="status" class="block text-xs font-bold text-slate-600 mb-2">সার্কুলারের অবস্থা</label>
                <select name="status" id="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>সব সার্কুলার</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>চলমান / মেয়াদ আছে</option>
                    <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>মেয়াদ শেষ</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-xs font-bold text-slate-600 mb-2">শিরোনাম / প্রতিষ্ঠান</label>
                <input type="search" name="search" id="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="সার্কুলার বা প্রতিষ্ঠানের নাম..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div>
                <label for="per_page" class="block text-xs font-bold text-slate-600 mb-2">প্রতি পেজে দেখান</label>
                <select name="per_page" id="per_page" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-200">
                    <?php foreach ($allowedPerPage as $n): ?>
                        <option value="<?php echo $n; ?>" <?php echo $per_page === $n ? 'selected' : ''; ?>><?php echo $n; ?> টি</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                <i class="fa-solid fa-magnifying-glass"></i> ফিল্টার করুন
            </button>
            <a href="circular_list.php" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                <i class="fa-solid fa-rotate-left"></i> রিসেট
            </a>
        </div>
    </form>

    <!-- Quick status counters -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="circular_list.php?status=all" class="bg-white border border-slate-200 rounded-xl p-4 hover:border-indigo-200 transition">
            <p class="text-[11px] font-semibold text-slate-500">সকল সার্কুলার</p>
            <p class="text-xl font-black text-slate-800 mt-1"><?php echo number_format($exm->getCircularListCount(0, 'all', '')); ?></p>
        </a>
        <a href="circular_list.php?status=active" class="bg-white border border-emerald-100 rounded-xl p-4 hover:border-emerald-300 transition">
            <p class="text-[11px] font-semibold text-emerald-700">চলমান সার্কুলার</p>
            <p class="text-xl font-black text-emerald-700 mt-1"><?php echo number_format($exm->getCircularListCount(0, 'active', '')); ?></p>
        </a>
        <a href="circular_list.php?status=expired" class="bg-white border border-rose-100 rounded-xl p-4 hover:border-rose-300 transition">
            <p class="text-[11px] font-semibold text-rose-700">মেয়াদ শেষ</p>
            <p class="text-xl font-black text-rose-700 mt-1"><?php echo number_format($exm->getCircularListCount(0, 'expired', '')); ?></p>
        </a>
    </div>

    <!-- Results table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-extrabold text-slate-800">সার্কুলার রেকর্ড</h2>
                <p class="text-[11px] text-slate-400 mt-1">ফিল্টার অনুযায়ী পাওয়া ফলাফল</p>
            </div>
            <p class="text-xs text-slate-500">
                দেখানো হচ্ছে <strong class="text-slate-800"><?php echo $fromRecord; ?>-<?php echo $toRecord; ?></strong>
                / <strong class="text-slate-800"><?php echo $total_circulars; ?></strong>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-14 text-center">নং</th>
                        <th class="px-4 py-3">ক্যাটাগরি</th>
                        <th class="px-4 py-3">শিরোনাম ও প্রতিষ্ঠান</th>
                        <th class="px-4 py-3">পদসংখ্যা</th>
                        <th class="px-4 py-3">শেষ তারিখ</th>
                        <th class="px-4 py-3 text-center">আবেদন লিংক</th>
                        <th class="px-4 py-3 text-center">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($getAll && $getAll->num_rows > 0): ?>
                        <?php $serial = $offset; while ($row = $getAll->fetch_assoc()): $serial++; ?>
                            <?php
                                $deadlineTime = !empty($row['deadline']) ? strtotime($row['deadline']) : false;
                                $isExpired = $deadlineTime ? $deadlineTime < strtotime(date('Y-m-d')) : false;
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 text-center font-bold text-slate-400"><?php echo $serial; ?></td>
                                <td class="px-4 py-3 font-semibold text-slate-800"><?php echo htmlspecialchars($row['category_name'] ?? 'ক্যাটাগরি নেই', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800"><?php echo htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-[11px] text-slate-400 mt-1"><?php echo htmlspecialchars($row['organization'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                                <td class="px-4 py-3 font-medium"><?php echo !empty($row['vacancy']) ? htmlspecialchars($row['vacancy'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($deadlineTime): ?>
                                        <span class="inline-flex px-2.5 py-1 text-[11px] font-bold rounded-lg <?php echo $isExpired ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                                            <?php echo date("d M, Y", $deadlineTime); ?>
                                            <?php echo $isExpired ? '(মেয়াদ শেষ)' : '(চলমান)'; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">তারিখ নেই</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if (!empty($row['apply_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['apply_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline font-bold text-[11px]">লিংক দেখুন <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i></a>
                                    <?php else: ?>
                                        <span class="text-slate-400">লিংক নেই</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <a href="circular_edit.php?id=<?php echo (int)$row['id']; ?>&return=<?php echo urlencode(http_build_query(array_merge($queryParams, array('page' => $pageNo)))); ?>"
                                           class="inline-flex items-center justify-center w-8 h-8 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <?php if (!$isCoAdmin): ?>
                                            <a href="circular_list.php?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $pageNo, 'del_id' => (int)$row['id']))), ENT_QUOTES, 'UTF-8'); ?>"
                                               onclick="return confirm('আপনি কি নিশ্চিত যে এই সার্কুলারটি মুছে ফেলতে চান?');"
                                               class="inline-flex items-center justify-center w-8 h-8 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg transition" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">আপনার নির্বাচিত ফিল্টারে কোনো সার্কুলার পাওয়া যায়নি।</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_circulars > 0 && $total_pages > 1): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                <p class="text-xs text-slate-500">পৃষ্ঠা <strong class="text-slate-800"><?php echo $pageNo; ?></strong> / <?php echo $total_pages; ?></p>
                <div class="flex flex-wrap items-center justify-center gap-1.5">
                    <?php
                        $first = 1;
                        $prev = max(1, $pageNo - 1);
                        $next = min($total_pages, $pageNo + 1);
                        $last = $total_pages;
                        $start = max(1, $pageNo - 2);
                        $end = min($total_pages, $pageNo + 2);
                    ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $first))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo $pageNo === 1 ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>">প্রথম</a>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $prev))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo $pageNo === 1 ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $p))), ENT_QUOTES, 'UTF-8'); ?>" class="min-w-9 px-3 py-2 text-center rounded-lg text-xs font-bold <?php echo $p === $pageNo ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>"><?php echo $p; ?></a>
                    <?php endfor; ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $next))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo $pageNo === $total_pages ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>"><i class="fa-solid fa-chevron-right"></i></a>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $last))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo $pageNo === $total_pages ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50'; ?>">শেষ</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer.php'; ?>