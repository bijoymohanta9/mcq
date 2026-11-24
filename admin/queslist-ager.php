<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');
    $exm = new Exam();
?>

<?php 
    if (isset($_GET['delque'])) {
        $quesno = (int)$_GET['delque'];
        $delQue = $exm->delQuestion($quesno);
    }
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Admin Panel - Question List</h1>
            <p class="text-slate-500 text-xs mt-1">Manage and organize all questions stored in the database</p>
        </div>
        <a href="quesadd.php" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add Question</span>
        </a>
    </div>

    <!-- Alert Message -->
    <?php if (isset($delQue)): ?>
        <div class="mb-6">
            <?php echo $delQue; ?>
        </div>
    <?php endif; ?>

    <!-- Questions Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">No</th>
                        <th class="py-4 px-6">Category</th>
                        <th class="py-4 px-6">Subject</th>
                        <th class="py-4 px-6">Question</th>
                        <th class="py-4 px-6 w-32 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                        $getData = $exm->getQueByOrder();
                        if ($getData) {
                            $i = 0;
                            while ($result = $getData->fetch_assoc()) {
                                $i++;
                    ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center font-bold text-slate-500"><?php echo $i; ?></td>
                            <td class="py-4 px-6 font-semibold text-indigo-600">
                                <?php echo htmlspecialchars($result['category_name'] ?? 'N/A'); ?>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-600">
                                <?php echo htmlspecialchars($result['subject_name'] ?? 'N/A'); ?>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800"><?php echo htmlspecialchars($result['ques']); ?></td>
                            <td class="py-4 px-6 text-center">
                                <a onclick="return confirm('Are You Sure to Remove')" 
                                   href="?delque=<?php echo $result['quesNo']; ?>" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white text-xs font-semibold transition">
                                    <i class="fa-regular fa-trash-can"></i>
                                    <span>Remove</span>
                                </a>
                            </td>
                        </tr>
                    <?php 
                            }
                        } else { 
                    ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-medium">No active questions found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>