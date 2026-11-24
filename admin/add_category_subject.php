<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/inc/header.php');
include_once ($filepath.'/../classes/Exam.php');
$exm = new Exam();

$msgCat = '';
$msgSub = '';
$msgType = 'success';

// Handle edit/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cat'])) {
        list($ok, $message) = $exm->updateCategory($_POST['category_id'] ?? 0, $_POST['category_name'] ?? '');
        $msgCat = $message;
        $msgType = $ok ? 'success' : 'error';
    } elseif (isset($_POST['delete_cat'])) {
        list($ok, $message) = $exm->deleteCategorySafely($_POST['category_id'] ?? 0);
        $msgCat = $message;
        $msgType = $ok ? 'success' : 'error';
    } elseif (isset($_POST['update_sub'])) {
        list($ok, $message) = $exm->updateSubject($_POST['subject_id'] ?? 0, $_POST['subject_name'] ?? '');
        $msgSub = $message;
        $msgType = $ok ? 'success' : 'error';
    } elseif (isset($_POST['delete_sub'])) {
        list($ok, $message) = $exm->deleteSubjectSafely($_POST['subject_id'] ?? 0);
        $msgSub = $message;
        $msgType = $ok ? 'success' : 'error';
    } elseif (isset($_POST['add_cat'])) {
        $cat_name = trim(mysqli_real_escape_string($db->link, $_POST['category_name'] ?? ''));
        if ($cat_name !== '') {
            $query = "INSERT INTO tbl_category(category_name) VALUES('$cat_name')";
            $msgCat = $db->insert($query) ? "ক্যাটাগরি সফলভাবে যুক্ত হয়েছে!" : "সমস্যা হয়েছে!";
        } else {
            $msgCat = "ক্যাটাগরির নাম ইনপুট দিন!";
        }
    } elseif (isset($_POST['add_sub'])) {
        $sub_name = trim(mysqli_real_escape_string($db->link, $_POST['subject_name'] ?? ''));
        if ($sub_name !== '') {
            $query = "INSERT INTO tbl_subject(subject_name) VALUES('$sub_name')";
            $msgSub = $db->insert($query) ? "বিষয় সফলভাবে যুক্ত হয়েছে!" : "সমস্যা হয়েছে!";
        } else {
            $msgSub = "বিষয়ের নাম ইনপুট দিন!";
        }
    }
}

$categories = $exm->getAllCategories();
$subjects = $exm->getAllSubjects();
?>

<div class="w-full max-w-5xl mx-auto my-8 px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">ক্যাটাগরি ও বিষয় যুক্ত করুন</h1>
        <p class="text-slate-500 text-xs mt-1">নতুন ক্যাটাগরি ও বিষয় যুক্ত, Edit এবং Delete করতে পারবেন</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Category -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-folder-plus text-indigo-600"></i> পরীক্ষার ধরন (Category)
            </h2>

            <?php if ($msgCat !== ''): ?>
                <div class="mb-4 text-xs font-semibold p-3 rounded-xl border <?php echo ($msgType === 'success') ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : 'text-rose-700 bg-rose-50 border-rose-100'; ?>">
                    <?php echo htmlspecialchars($msgCat, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">ক্যাটাগরির নাম <span class="text-rose-500">*</span></label>
                    <input type="text" name="category_name" placeholder="যেমন: বিসিএস প্রিলিমিনারি" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" name="add_cat" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> ক্যাটাগরি সেভ করুন
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold text-slate-700">বর্তমান ক্যাটাগরি তালিকা</h3>
                    <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">মোট <?php echo $categories ? $categories->num_rows : 0; ?> টি</span>
                </div>
                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    <?php if ($categories && $categories->num_rows > 0): $n=0; while ($cat = $categories->fetch_assoc()): $n++; ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 flex-shrink-0 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px] font-bold"><?php echo $n; ?></span>
                                <span class="flex-1 min-w-0 text-xs font-semibold text-slate-700 break-words"><?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <details class="relative">
                                    <summary class="cursor-pointer list-none px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-bold"><i class="fa-solid fa-pen"></i> Edit</summary>
                                    <form method="post" class="mt-2 p-3 bg-white border border-slate-200 rounded-xl space-y-2">
                                        <input type="hidden" name="category_id" value="<?php echo (int)$cat['id']; ?>">
                                        <input type="text" name="category_name" required value="<?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full border border-slate-200 rounded-lg p-2 text-xs">
                                        <button type="submit" name="update_cat" class="w-full px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold">Save Changes</button>
                                    </form>
                                </details>
                                <form method="post" onsubmit="return confirm('এই ক্যাটাগরি Delete করতে চান? ব্যবহৃত হলে Delete হবে না।');">
                                    <input type="hidden" name="category_id" value="<?php echo (int)$cat['id']; ?>">
                                    <button type="submit" name="delete_cat" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                        <p class="text-xs text-slate-400 text-center py-5 bg-slate-50 rounded-xl">এখনো কোনো ক্যাটাগরি যুক্ত করা হয়নি।</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Subject -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-book text-indigo-600"></i> বিষয় (Subject)
            </h2>

            <?php if ($msgSub !== ''): ?>
                <div class="mb-4 text-xs font-semibold p-3 rounded-xl border <?php echo ($msgType === 'success') ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : 'text-rose-700 bg-rose-50 border-rose-100'; ?>">
                    <?php echo htmlspecialchars($msgSub, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">বিষয়ের নাম <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject_name" placeholder="যেমন: বাংলা ভাষা ও সাহিত্য" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" name="add_sub" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> বিষয় সেভ করুন
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold text-slate-700">বর্তমান বিষয় তালিকা</h3>
                    <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">মোট <?php echo $subjects ? $subjects->num_rows : 0; ?> টি</span>
                </div>
                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    <?php if ($subjects && $subjects->num_rows > 0): $n=0; while ($sub = $subjects->fetch_assoc()): $n++; ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 flex-shrink-0 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px] font-bold"><?php echo $n; ?></span>
                                <span class="flex-1 min-w-0 text-xs font-semibold text-slate-700 break-words"><?php echo htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <details class="relative">
                                    <summary class="cursor-pointer list-none px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-bold"><i class="fa-solid fa-pen"></i> Edit</summary>
                                    <form method="post" class="mt-2 p-3 bg-white border border-slate-200 rounded-xl space-y-2">
                                        <input type="hidden" name="subject_id" value="<?php echo (int)$sub['id']; ?>">
                                        <input type="text" name="subject_name" required value="<?php echo htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full border border-slate-200 rounded-lg p-2 text-xs">
                                        <button type="submit" name="update_sub" class="w-full px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold">Save Changes</button>
                                    </form>
                                </details>
                                <form method="post" onsubmit="return confirm('এই বিষয় Delete করতে চান? ব্যবহৃত হলে Delete হবে না।');">
                                    <input type="hidden" name="subject_id" value="<?php echo (int)$sub['id']; ?>">
                                    <button type="submit" name="delete_sub" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                        <p class="text-xs text-slate-400 text-center py-5 bg-slate-50 rounded-xl">এখনো কোনো বিষয় যুক্ত করা হয়নি।</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
