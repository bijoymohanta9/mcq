<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');

    // exam_history.php এর শুরুতে এই লাইনটি বসান[cite: 10]
    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");
    
    $exm = new Exam();

    // Filter Inputs (GET)
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $subject_id  = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
    $status      = isset($_GET['status']) ? trim($_GET['status']) : '';
    if (!in_array($status, array('Passed', 'Failed'), true)) {
        $status = '';
    }
    $search      = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Pagination Setup
    $per_page      = 10;
    $total_records = $exm->getFilteredExamHistoryCount($userId, $category_id, $subject_id, $status, $search);
    $total_pages   = max(1, (int)ceil($total_records / $per_page));
    $page          = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $page          = min($page, $total_pages);
    $offset        = ($page - 1) * $per_page;

    $historyData   = $exm->getFilteredExamHistoryPaginated($userId, $category_id, $subject_id, $status, $search, $per_page, $offset);
    $categories    = $exm->getAllCategories();
    $subjects      = $exm->getAllSubjects();

    $queryParams = array(
        'category_id' => $category_id,
        'subject_id'  => $subject_id,
        'status'      => $status,
        'search'      => $search
    );
?>

<div class="w-full max-w-5xl mx-auto my-8 px-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">আমার পরীক্ষার রেকর্ড</h1>
        <p class="text-slate-500 text-xs mt-1">আপনার দেওয়া সকল পরীক্ষার ফলাফল ও পারফরম্যান্স হিস্ট্রি</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="exam_history.php" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-600 mb-1.5">ক্যাটাগরি</label>
                <select name="category_id" id="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="0">সকল ক্যাটাগরি</option>
                    <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($category_id === (int)$cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
                <label for="subject_id" class="block text-xs font-bold text-slate-600 mb-1.5">বিষয়</label>
                <select name="subject_id" id="subject_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="0">সকল বিষয়</option>
                    <?php if ($subjects): while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo (int)$sub['id']; ?>" <?php echo ($subject_id === (int)$sub['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
                <label for="status" class="block text-xs font-bold text-slate-600 mb-1.5">স্ট্যাটাস</label>
                <select name="status" id="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">সকল স্ট্যাটাস</option>
                    <option value="Passed" <?php echo ($status === 'Passed') ? 'selected' : ''; ?>>উত্তীর্ণ</option>
                    <option value="Failed" <?php echo ($status === 'Failed') ? 'selected' : ''; ?>>অনুত্তীর্ণ</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-xs font-bold text-slate-600 mb-1.5">খুঁজুন (আইডি / বিষয়)</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="EXM-... বা বিষয়" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 mt-4">
            <span class="text-xs text-slate-500">মোট রেকর্ড: <strong class="text-slate-800"><?php echo $total_records; ?></strong></span>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                    <i class="fa-solid fa-filter"></i> ফিল্টার করুন
                </button>
                <a href="exam_history.php" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    <i class="fa-solid fa-rotate-left"></i> রিসেট
                </a>
            </div>
        </div>
    </form>

    <!-- History Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4">ইউনিক আইডি</th>
                        <th class="py-3.5 px-4">বিষয় ও ক্যাটাগরি</th>
                        <th class="py-3.5 px-4">তারিখ ও সময়</th>
                        <th class="py-3.5 px-4 text-center">স্কোর (প্রাপ্ত / মোট)</th>
                        <th class="py-3.5 px-4 text-center">সাফল্যের হার</th>
                        <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-3.5 px-4 text-center">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <?php 
                    if ($historyData && $historyData->num_rows > 0) {
                        while ($row = $historyData->fetch_assoc()) {
                    ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 font-mono font-bold text-indigo-600">
                                <?php echo $row['attempt_code']; ?>
                            </td>
                            <td class="py-4 px-4">
                                <h4 class="font-bold text-slate-800">
                                    <?php echo htmlspecialchars($row['subject_name'] ?? 'সাধারণ পরীক্ষা'); ?>
                                </h4>
                                <p class="text-xs text-slate-500">
                                    <?php echo htmlspecialchars($row['category_name'] ?? 'সকল ক্যাটাগরি'); ?>
                                </p>
                            </td>
                            <td class="py-4 px-4 text-slate-500">
                                <?php echo date('d M Y, h:i A', strtotime($row['exam_date'])); ?>
                            </td>
                            <td class="py-4 px-4 text-center font-semibold">
                                <span class="text-emerald-600"><?php echo $row['correct_answers']; ?></span> / <?php echo $row['total_questions']; ?>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-xs <?php echo ($row['percentage'] >= 50) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'; ?>">
                                    <?php echo $row['percentage']; ?>%
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <?php if($row['status'] == 'Passed'): ?>
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-emerald-100 text-emerald-800">উত্তীর্ণ</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-rose-100 text-rose-800">অনুত্তীর্ণ</span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Action Button -->
                            <td class="py-4 px-4 text-center">
                                <a href="viewans.php?code=<?php echo $row['attempt_code']; ?>" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-3 py-1.5 rounded-lg border border-indigo-200 transition font-medium text-[11px]">
                                    <i class="fa-solid fa-eye"></i> বিস্তারিত
                                </a>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-8 text-slate-400'>আপনি এখনো কোনো পরীক্ষায় অংশ নেননি।</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <!-- Pagination Bar -->
        <?php if ($total_records > 0): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-slate-100 text-xs">
                <p class="text-slate-500">
                    পৃষ্ঠা <strong class="text-slate-700"><?php echo $page; ?></strong> / <strong class="text-slate-700"><?php echo $total_pages; ?></strong>
                    (মোট রেকর্ড: <strong class="text-slate-700"><?php echo $total_records; ?></strong>)
                </p>
                <div class="flex flex-wrap items-center justify-center gap-1.5">
                    <?php
                    $prevPage = max(1, $page - 1);
                    $nextPage = min($total_pages, $page + 1);
                    ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $prevPage))), ENT_QUOTES, 'UTF-8'); ?>"
                       class="px-3 py-1.5 rounded-lg font-bold <?php echo ($page <= 1) ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>">
                        পূর্ববর্তী
                    </a>

                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $p))), ENT_QUOTES, 'UTF-8'); ?>"
                           class="px-3 py-1.5 rounded-lg font-bold transition <?php echo ($p === $page) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>">
                            <?php echo $p; ?>
                        </a>
                    <?php endfor; ?>

                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $nextPage))), ENT_QUOTES, 'UTF-8'); ?>"
                       class="px-3 py-1.5 rounded-lg font-bold <?php echo ($page >= $total_pages) ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>">
                        পরবর্তী
                    </a>
                </div>
            </div>
        <?php endif; ?>
       
    </div>
</div>

<?php include 'inc/footer.php'; ?>