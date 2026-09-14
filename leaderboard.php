<?php 
include 'inc/header.php';
Session::checkSession();

$currentUserId = Session::get("userId");

$categoryId    = isset($_GET['category_id']) && $_GET['category_id'] != '' ? (int)$_GET['category_id'] : 1;
$subjectId     = isset($_GET['subject_id']) && $_GET['subject_id'] != '' ? (int)$_GET['subject_id'] : 0;
$attemptsCount = isset($_GET['attempts']) && $_GET['attempts'] != '' ? (int)$_GET['attempts'] : 0;

$categories      = $exm->getAllCategories();
$subjects        = $exm->getAllSubjects();
$leaderboardData = $exm->getFilteredLeaderboard($categoryId, $subjectId, $attemptsCount);
?>

<div class="max-w-6xl mx-auto my-10 px-4">
    
    <!-- Filter Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
        <h2 class="text-xl font-black text-slate-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-trophy text-amber-500"></i> তুলনামূলক লিডারবোর্ড
        </h2>
        
        <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Category -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ক্যাটাগরি</label>
                <select name="category_id" class="w-full border border-slate-300 rounded-xl p-2.5 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                    <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <!-- Subject -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">সাবজেক্ট</label>
                <select name="subject_id" class="w-full border border-slate-300 rounded-xl p-2.5 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="0">সকল সাবজেক্ট</option>
                    <?php if ($subjects): while ($sub = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo ($subjectId == $sub['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sub['subject_name']); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <!-- Exam Attempts Count -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">পরীক্ষার সংখ্যা (ঐচ্ছিক)</label>
                <input type="number" min="1" name="attempts" value="<?php echo $attemptsCount > 0 ? $attemptsCount : ''; ?>" placeholder="যেমন: 5" class="w-full border border-slate-300 rounded-xl p-2.5 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold p-2.5 rounded-xl text-xs transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i> ফিল্টার করুন
                </button>
            </div>
        </form>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase font-bold tracking-wider">
                        <th class="p-4 text-center">র‍্যাংক</th>
                        <th class="p-4">ব্যবহারকারী</th>
                        <th class="p-4 text-center">মোট পরীক্ষা</th>
                        <th class="p-4 text-center">গড় মার্কস (%)</th>
                        <th class="p-4 text-right">মোট স্কোর</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    <?php 
                    if ($leaderboardData && $leaderboardData->num_rows > 0): 
                        $rank = 0;
                        while ($row = $leaderboardData->fetch_assoc()):
                            $rank++;
                            $isCurrentUser = ($row['userId'] == $currentUserId);
                    ?>
                        <tr class="<?php echo $isCurrentUser ? 'bg-indigo-50/70 font-bold border-l-4 border-l-indigo-600' : 'hover:bg-slate-50/50'; ?>">
                            <td class="p-4 text-center font-black">
                                <?php 
                                    if ($rank == 1) echo '🥇 1';
                                    elseif ($rank == 2) echo '🥈 2';
                                    elseif ($rank == 3) echo '🥉 3';
                                    else echo $rank;
                                ?>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                    <?php if ($isCurrentUser): ?>
                                        <span class="ml-2 text-[10px] bg-indigo-600 text-white px-2 py-0.5 rounded-full font-bold">আপনি</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-slate-400"><?php echo htmlspecialchars($row['email']); ?></div>
                            </td>
                            <td class="p-4 text-center font-semibold text-slate-600">
                                <?php echo $row['total_attempt']; ?> টি
                            </td>
                            <td class="p-4 text-center font-semibold text-emerald-600">
                                <?php echo round($row['avg_percentage'], 1); ?>%
                            </td>
                            <td class="p-4 text-right font-black text-indigo-600 text-base">
                                <?php echo $row['total_score']; ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 font-bold">
                                এই ফিল্টার অনুযায়ী কোনো ডাটা পাওয়া যায়নি!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>