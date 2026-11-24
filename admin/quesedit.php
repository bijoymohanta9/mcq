<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/inc/header.php');
include_once($filepath . '/../classes/Exam.php');

$exm = new Exam();

$quesId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($quesId <= 0) {
    echo "<script>window.location = 'queslist.php';</script>";
    exit;
}

$updateMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_question'])) {
    $updateMsg = $exm->updateQuestionById($quesId, $_POST);
}

$questionRow = $exm->getEditableQuestionById($quesId);
if (!$questionRow) {
    echo "<div class='w-full max-w-4xl mx-auto my-8 px-4'><div class='p-4 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold'>প্রশ্নটি পাওয়া যায়নি। <a href='queslist.php' class='underline ml-2'>Back to List</a></div></div>";
    include_once($filepath . '/inc/footer.php');
    exit;
}

$ansRes = $exm->getEditableAnswersByQuesId($quesId);
$options = array(1 => '', 2 => '', 3 => '', 4 => '');
$selectedRight = 1;
if ($ansRes && $ansRes->num_rows > 0) {
    $idx = 0;
    while ($aRow = $ansRes->fetch_assoc()) {
        $idx++;
        if ($idx <= 4) {
            $options[$idx] = $aRow['ans'];
            if ((string)$aRow['rightAns'] === '1') {
                $selectedRight = $idx;
            }
        }
    }
}

$categories = $exm->getAllCategories();
$subjects = $exm->getAllSubjects();
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit Question</h1>
            <p class="text-slate-500 text-xs mt-1">Update question details, options, and correct answer</p>
        </div>
        <a href="queslist.php" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
            ← Back to Question List
        </a>
    </div>

    <?php if (!empty($updateMsg)): ?>
        <div class="mb-5"><?php echo $updateMsg; ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Category</label>
                <select name="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700">
                    <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo ((int)$questionRow['category_id'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Subject</label>
                <select name="subject_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700">
                    <?php if ($subjects): while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo (int)$sub['id']; ?>" <?php echo ((int)$questionRow['subject_id'] === (int)$sub['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div>
				<label class="block text-xs font-bold text-slate-600 mb-1.5">Question No</label>
				<input type="number" name="quesNo" value="<?php echo (int)$questionRow['quesNo']; ?>" readonly class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed px-3 py-2.5 text-xs">
			</div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Question</label>
            <textarea name="ques" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-700"><?php echo htmlspecialchars($questionRow['ques'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php for ($op = 1; $op <= 4; $op++): ?>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Option <?php echo $op; ?></label>
                    <input type="text" name="ans<?php echo $op; ?>" value="<?php echo htmlspecialchars($options[$op] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-700">
                </div>
            <?php endfor; ?>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Correct Answer</label>
            <select name="rightAns" class="w-full sm:w-64 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700">
                <option value="1" <?php echo ($selectedRight === 1) ? 'selected' : ''; ?>>Option 1</option>
                <option value="2" <?php echo ($selectedRight === 2) ? 'selected' : ''; ?>>Option 2</option>
                <option value="3" <?php echo ($selectedRight === 3) ? 'selected' : ''; ?>>Option 3</option>
                <option value="4" <?php echo ($selectedRight === 4) ? 'selected' : ''; ?>>Option 4</option>
            </select>
        </div>

        <div class="pt-2 flex items-center gap-2">
            <button type="submit" name="update_question" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                Save Changes
            </button>
            <a href="queslist.php" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include_once($filepath . '/inc/footer.php'); ?>