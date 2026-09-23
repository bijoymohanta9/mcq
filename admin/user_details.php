<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');
    $exm = new Exam();

    // GET থেকে ID রিড করা (userId, user_id, id তিনটিই হ্যান্ডেল করবে)
    $userId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['userid']) ? (int)$_GET['userid'] : (isset($_GET['userId']) ? (int)$_GET['userId'] : 0));

    // আইডি না পাওয়া গেলে ফ্রেন্ডলি এরর
    if ($userId <= 0) {
        echo "<div class='w-full max-w-xl mx-auto my-12 p-6 bg-amber-50 border border-amber-200 rounded-2xl text-center'>";
        echo "<h2 class='text-lg font-bold text-amber-800 mb-2'>URL Parameter Issue</h2>";
        echo "<p class='text-xs text-amber-700 mb-4'>URL-এ কোনো ID পাওয়া যায়নি! আপনার URL দেখাচ্ছে: <code class='bg-amber-100 px-2 py-1 rounded'>".$_SERVER['REQUEST_URI']."</code></p>";
        echo "<p class='text-xs text-slate-500'>Leaderboard-এর লিংকে <code class='bg-slate-100 px-1.5 py-0.5 rounded'>?id=userid_value</code> সঠিকভাবে পাস হচ্ছে কিনা নিশ্চিত করুন।</p>";
        echo "</div>";
        include 'inc/footer.php';
        exit();
    }

    // ১. tbl_user টেবিল থেকে userId (CamelCase) দিয়ে ডাটা ফেচ
    $userQuery = "SELECT * FROM tbl_user WHERE userId = '$userId'";
    $userData  = $db->select($userQuery);
    $user      = ($userData && $userData->num_rows > 0) ? $userData->fetch_assoc() : null;

    if (!$user) {
        echo "<div class='w-full max-w-xl mx-auto my-12 p-6 bg-rose-50 border border-rose-200 rounded-2xl text-center'>";
        echo "<h2 class='text-lg font-bold text-rose-800 mb-2'>User Not Found in Database</h2>";
        echo "<p class='text-xs text-rose-700 mb-4'>ID #{$userId} এর জন্য <code class='bg-rose-100 px-1.5 py-0.5 rounded'>tbl_user</code> টেবিলে কোনো রেকর্ড পাওয়া যায়নি।</p>";
        echo "</div>";
        include 'inc/footer.php';
        exit();
    }

    // ২. tbl_exam_history থেকে user_id দিয়ে সামারি তথ্য আনা
    $statsQuery = "SELECT 
                    COUNT(*) as total_exams, 
                    COALESCE(SUM(total_marks), 0) as total_score, 
                    COALESCE(AVG(total_marks), 0) as avg_score,
                    COALESCE(MAX(total_marks), 0) as highest_score
                   FROM tbl_exam_history 
                   WHERE user_id = '$userId'";
    $statsData  = $db->select($statsQuery);
    $stats      = ($statsData) ? $statsData->fetch_assoc() : null;

    // ৩. এক্সাম হিস্ট্রি কুয়েরি
    $historyQuery = "SELECT h.*, c.category_name 
                     FROM tbl_exam_history h
                     LEFT JOIN tbl_category c ON h.category_id = c.id
                     WHERE h.user_id = '$userId'
                     ORDER BY h.id DESC";
    $historyData = $db->select($historyQuery);
?>

<div class="w-full max-w-7xl mx-auto my-8 px-4">
    
    <!-- Navigation Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="leaderboard.php" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-indigo-600"></i>
                    <span>Candidate Profile Overview</span>
                </h1>
                <p class="text-slate-500 text-xs mt-0.5">Detailed performance metrics for Candidate #<?php echo $userId; ?></p>
            </div>
        </div>
        <div>
            <a href="leaderboard.php" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-trophy"></i>
                <span>Back to Leaderboard</span>
            </a>
        </div>
    </div>

    <!-- Candidate Profile & Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- User Info Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-indigo-600/20">
                        <?php echo strtoupper(substr($user['name'] ?? $user['username'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 leading-tight"><?php echo htmlspecialchars($user['name'] ?? $user['username'] ?? 'N/A'); ?></h2>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            Candidate ID: #<?php echo $userId; ?>
                        </span>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-medium text-slate-400 flex items-center gap-2"><i class="fa-solid fa-user w-4"></i> Username:</span>
                        <span class="font-bold text-slate-700"><?php echo htmlspecialchars($user['username'] ?? $user['name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-medium text-slate-400 flex items-center gap-2"><i class="fa-solid fa-envelope w-4"></i> Email:</span>
                        <span class="font-bold text-slate-700"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="font-medium text-slate-400 flex items-center gap-2"><i class="fa-solid fa-shield-halved w-4"></i> Status:</span>
                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px] <?php echo (isset($user['status']) && $user['status'] == 1) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'; ?>">
                            <?php echo (isset($user['status']) && $user['status'] == 1) ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold mb-3">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Exams</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo (int)($stats['total_exams'] ?? 0); ?></h3>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold mb-3">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aggregate Score</p>
                    <h3 class="text-2xl font-black text-indigo-600 mt-1"><?php echo (int)($stats['total_score'] ?? 0); ?> <span class="text-xs font-bold text-slate-400">pts</span></h3>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-3">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Average Score</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo number_format((float)($stats['avg_score'] ?? 0), 1); ?></h3>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold mb-3">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Best Performance</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo (int)($stats['highest_score'] ?? 0); ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- Exam History Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                <span>Complete Examination Log</span>
            </h3>
            <span class="text-xs text-slate-400 font-medium">Showing all historical submissions</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">#</th>
                        <th class="py-4 px-6">Category</th>
                        <th class="py-4 px-6 text-center">Obtained Marks</th>
                        <th class="py-4 px-6 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                        if ($historyData && $historyData->num_rows > 0) {
                            $sl = 0;
                            while ($hRow = $historyData->fetch_assoc()) {
                                $sl++;
                                $score = (int)($hRow['total_marks'] ?? 0);
                    ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center font-bold text-slate-400"><?php echo $sl; ?></td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800"><?php echo htmlspecialchars($hRow['category_name'] ?? 'General Category'); ?></div>
                            </td>
                            <td class="py-4 px-6 text-center font-black text-indigo-600 text-sm"><?php echo $score; ?></td>
                            <td class="py-4 px-6 text-right text-slate-400 font-medium">
                                <?php 
                                    $dateVal = $hRow['date'] ?? $hRow['created_at'] ?? '';
                                    echo $dateVal ? date('d M, Y - h:i A', strtotime($dateVal)) : 'N/A';
                                ?>
                            </td>
                        </tr>
                    <?php 
                            }
                        } else {
                    ?>
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 font-medium">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                No exam history records found for this candidate.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>