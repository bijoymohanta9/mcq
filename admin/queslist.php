<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/inc/header.php');
include_once($filepath . '/../classes/Exam.php');

$exm = new Exam();

if (isset($_GET['delque'])) {
    $quesno = (int)$_GET['delque'];
    $delQue = $exm->delQuestion($quesno);
}

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$subject_id  = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$search      = isset($_GET['search']) ? trim($_GET['search']) : '';

$allowedPerPage = array(10, 20, 50, 100);
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
if (!in_array($per_page, $allowedPerPage, true)) $per_page = 10;

$total_questions = $exm->getQuestionListCount($category_id, $subject_id, $search);
$total_pages = max(1, (int)ceil($total_questions / $per_page));
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

$getData = $exm->getQuestionListPaginated($category_id, $subject_id, $search, $per_page, $offset);
$categories = $exm->getAllCategories();
$subjects = $exm->getAllSubjects();

$queryParams = array(
    'category_id' => $category_id,
    'subject_id' => $subject_id,
    'search' => $search,
    'per_page' => $per_page
);

$start_number = ($total_questions > 0) ? $offset + 1 : 0;
$end_number = min($offset + $per_page, $total_questions);
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Admin Panel - Question List</h1>
            <p class="text-slate-500 text-xs mt-1">Manage and organize all questions stored in the database</p>
        </div>
        <a href="quesadd.php" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus text-xs"></i><span>Add Question</span>
        </a>
    </div>

    <?php if (isset($delQue)): ?>
        <div class="mb-6"><?php echo $delQue; ?></div>
    <?php endif; ?>

    <form method="GET" action="queslist.php" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-600 mb-2">Category</label>
                <select name="category_id" id="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="0">All Categories</option>
                    <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($category_id === (int)$cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
                <label for="subject_id" class="block text-xs font-bold text-slate-600 mb-2">Subject</label>
                <select name="subject_id" id="subject_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="0">All Subjects</option>
                    <?php if ($subjects): while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo (int)$sub['id']; ?>" <?php echo ($subject_id === (int)$sub['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
                <label for="search" class="block text-xs font-bold text-slate-600 mb-2">Search Question</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Type question keyword..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div>
                <label for="per_page" class="block text-xs font-bold text-slate-600 mb-2">Rows Per Page</label>
                <select name="per_page" id="per_page" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <?php foreach ($allowedPerPage as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo ($per_page === $option) ? 'selected' : ''; ?>><?php echo $option; ?> per page</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                <i class="fa-solid fa-filter"></i> Apply Filter
            </button>
            <a href="queslist.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 py-4 border-b border-slate-100">
            <p class="text-xs font-bold text-slate-700">Question Records</p>
            <p class="text-xs text-slate-500">
                Showing <span class="font-bold text-slate-700"><?php echo $start_number; ?>-<?php echo $end_number; ?></span>
                of <span class="font-bold text-slate-700"><?php echo $total_questions; ?></span> questions
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">No</th>
                        <th class="py-4 px-6">Category</th>
                        <th class="py-4 px-6">Subject</th>
                        <th class="py-4 px-6">Question</th>
                        <th class="py-4 px-6 w-44 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if ($getData && $getData->num_rows > 0): ?>
                        <?php $i = $offset; while ($result = $getData->fetch_assoc()): $i++; ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 text-center font-bold text-slate-500"><?php echo $i; ?></td>
                                <td class="py-4 px-6 font-semibold text-indigo-600"><?php echo htmlspecialchars($result['category_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="py-4 px-6 font-medium text-slate-600"><?php echo htmlspecialchars($result['subject_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="py-4 px-6 font-medium text-slate-800"><?php echo htmlspecialchars($result['ques'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="py-4 px-6 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <a href="quesedit.php?id=<?php echo (int)$result['id']; ?>"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white text-xs font-semibold transition">
                                            <i class="fa-regular fa-pen-to-square"></i><span>Edit</span>
                                        </a>
                                        <a onclick="return confirm('Are You Sure to Remove')"
                                           href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $page, 'delque' => $result['quesNo']))), ENT_QUOTES, 'UTF-8'); ?>"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white text-xs font-semibold transition">
                                            <i class="fa-regular fa-trash-can"></i><span>Remove</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="py-10 text-center text-slate-400 font-medium">No questions found for the selected filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_questions > 0 && $total_pages > 1): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                <p class="text-xs text-slate-500">Page <span class="font-bold text-slate-700"><?php echo $page; ?></span> of <span class="font-bold text-slate-700"><?php echo $total_pages; ?></span></p>
                <div class="flex flex-wrap items-center justify-center gap-1.5">
                    <?php
                    $previousPage = max(1, $page - 1);
                    $nextPage = min($total_pages, $page + 1);
                    $windowStart = max(1, $page - 2);
                    $windowEnd = min($total_pages, $page + 2);
                    ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => 1))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo ($page === 1) ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>">First</a>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $previousPage))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo ($page === 1) ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php for ($p = $windowStart; $p <= $windowEnd; $p++): ?>
                        <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $p))), ENT_QUOTES, 'UTF-8'); ?>" class="min-w-9 px-3 py-2 text-center rounded-lg text-xs font-bold transition <?php echo ($p === $page) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>"><?php echo $p; ?></a>
                    <?php endfor; ?>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $nextPage))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo ($page === $total_pages) ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>"><i class="fa-solid fa-chevron-right"></i></a>
                    <a href="?<?php echo htmlspecialchars(http_build_query(array_merge($queryParams, array('page' => $total_pages))), ENT_QUOTES, 'UTF-8'); ?>" class="px-3 py-2 rounded-lg text-xs font-bold <?php echo ($page === $total_pages) ? 'pointer-events-none bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'; ?>">Last</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer.php'; ?>