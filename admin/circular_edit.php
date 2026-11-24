<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../classes/Exam.php';
$exm = new Exam();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: circular_list.php");
    exit();
}

// Filter থেকে আসা query string, যাতে Save/Cancel-এর পর আগের তালিকায় ফেরা যায়
$returnQuery = isset($_GET['return']) ? $_GET['return'] : ($_POST['return'] ?? '');
$returnQuery = ltrim($returnQuery, '?');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_circular'])) {
    $message = $exm->updateCircular($id, $_POST);
    if (strpos($message, 'সফলভাবে আপডেট হয়েছে') !== false) {
        $_SESSION['msg'] = $message;
        $destination = 'circular_list.php';
        if ($returnQuery !== '') {
            $destination .= '?' . $returnQuery;
        }
        header("Location: " . $destination);
        exit();
    }
}

$circularResult = $exm->getCircularById($id);
if (!$circularResult || $circularResult->num_rows === 0) {
    include_once 'inc/header.php';
    echo "<div class='max-w-3xl mx-auto my-10 p-5 bg-rose-50 text-rose-700 rounded-xl'>সার্কুলারটি পাওয়া যায়নি।</div>";
    include 'inc/footer.php';
    exit();
}
$circular = $circularResult->fetch_assoc();
$categories = $exm->getAllCategories();

function circularEditValue($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

include_once 'inc/header.php';
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200">
            <h1 class="text-xl font-black text-slate-800">সার্কুলার Edit করুন</h1>
            <p class="text-xs text-slate-500 mt-1">সার্কুলারের তথ্য পরিবর্তন করে Save Changes চাপুন</p>
        </div>

        <div class="p-6">
            <?php if (!empty($message)) echo $message; ?>

            <form method="POST" action="circular_edit.php" class="space-y-5">
                <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
                <input type="hidden" name="return" value="<?php echo circularEditValue($returnQuery); ?>">

                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-700 mb-2">ক্যাটাগরি <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">ক্যাটাগরি নির্বাচন করুন</option>
                        <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo ((int)$circular['category_id'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                                <?php echo circularEditValue($cat['category_name']); ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-xs font-bold text-slate-700 mb-2">সার্কুলারের শিরোনাম <span class="text-rose-500">*</span></label>
                    <input id="title" name="title" type="text" required value="<?php echo circularEditValue($circular['title']); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <div>
                    <label for="organization" class="block text-xs font-bold text-slate-700 mb-2">প্রতিষ্ঠানের নাম <span class="text-rose-500">*</span></label>
                    <input id="organization" name="organization" type="text" required value="<?php echo circularEditValue($circular['organization']); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vacancy" class="block text-xs font-bold text-slate-700 mb-2">পদসংখ্যা</label>
                        <input id="vacancy" name="vacancy" type="text" value="<?php echo circularEditValue($circular['vacancy']); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label for="deadline" class="block text-xs font-bold text-slate-700 mb-2">আবেদনের শেষ তারিখ <span class="text-rose-500">*</span></label>
                        <input id="deadline" name="deadline" type="date" required value="<?php echo circularEditValue($circular['deadline']); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                </div>

                <div>
                    <label for="apply_url" class="block text-xs font-bold text-slate-700 mb-2">আবেদন লিংক <span class="text-rose-500">*</span></label>
                    <input id="apply_url" name="apply_url" type="url" required value="<?php echo circularEditValue($circular['apply_url']); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <div>
                    <label for="pdf_url" class="block text-xs font-bold text-slate-700 mb-2">PDF / Image URL</label>
                    <input id="pdf_url" name="pdf_url" type="text" value="<?php echo circularEditValue($circular['pdf_url']); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-200">
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" name="update_circular" value="1" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                    <a href="circular_list.php<?php echo $returnQuery !== '' ? '?' . circularEditValue($returnQuery) : ''; ?>" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        <i class="fa-solid fa-arrow-left"></i> ফিরে যান
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
