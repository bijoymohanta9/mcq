<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');
    $exm = new Exam();

    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<div class="w-full max-w-7xl mx-auto my-8 px-4">
    
    <!-- Page Header & Global Filters -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-500"></i>
                <span>Admin Leaderboard Overview</span>
            </h1>
            <p class="text-slate-500 text-xs mt-1">Monitor candidate rankings, total exams taken, and overall category performance.</p>
        </div>

        <!-- Filters Form: Category & Search -->
        <form method="GET" action="" class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search student name/email..." class="pl-8 pr-3 py-2 bg-white border border-slate-300 text-slate-700 text-xs rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm w-56 font-medium">
            </div>

            <!-- Category Filter Dropdown -->
            <select name="category_id" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-700 text-xs rounded-xl p-2.5 focus:ring-2 focus:ring-indigo-500 shadow-sm outline-none font-semibold">
                <option value="0">-- All Categories --</option>
                <?php 
                    $getCats = $exm->getCategories();
                    if ($getCats) {
                        while ($cat = $getCats->fetch_assoc()) {
                            $selected = ($category_id == $cat['id']) ? 'selected' : '';
                            echo "<option value='".$cat['id']."' $selected>".$cat['category_name']."</option>";
                        }
                    }
                ?>
            </select>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition">
                Filter
            </button>
        </form>
    </div>

    <?php 
        // Data Fetching Logic (If category selected or overall)
        $getLeaderboard = $exm->getLeaderboardByCategory($category_id); // Adjust query in backend if search filter is passed
        $totalCandidates = 0;
        $totalExamsSubmitted = 0;
        $topScore = 0;

        // Extract metrics for quick cards if data exists
        if ($getLeaderboard && $getLeaderboard->num_rows > 0) {
            $totalCandidates = $getLeaderboard->num_rows;
            // Additional calculations can be assigned from DB aggregated queries
        }
    ?>

    <!-- Admin Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Performers</p>
                <h3 class="text-xl font-black text-slate-800 mt-1"><?php echo $totalCandidates; ?></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filter Mode</p>
                <h3 class="text-sm font-bold text-indigo-600 mt-1">
                    <?php echo $category_id > 0 ? "Category #".$category_id : "All Categories"; ?>
                </h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Top Position Rank</p>
                <h3 class="text-sm font-bold text-emerald-600 mt-1">🥇 Champion Active</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">System Status</p>
                <h3 class="text-sm font-bold text-slate-700 mt-1">Live Tracking</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 w-20 text-center">Rank</th>
                        <th class="py-4 px-6">User / Candidate</th>
                        <th class="py-4 px-6">Email Address</th>
                        <th class="py-4 px-6 text-center">Exams Attempted</th>
                        <th class="py-4 px-6 text-center">Total Score</th>
                        <th class="py-4 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                        if ($getLeaderboard && $getLeaderboard->num_rows > 0) {
                            $rank = 0;
                            while ($row = $getLeaderboard->fetch_assoc()) {
                                $rank++;
                                
                                // Search filter condition on PHP side if needed
                                if ($search_query != '' && strpos(strtolower($row['name']), strtolower($search_query)) === false && strpos(strtolower($row['email']), strtolower($search_query)) === false) {
                                    continue;
                                }

                                // Rank Formatting
                                $badgeClass = "bg-slate-100 text-slate-600";
                                $icon = "";
                                if ($rank == 1) {
                                    $badgeClass = "bg-amber-100 text-amber-700 border border-amber-300 font-black";
                                    $icon = "<i class='fa-solid fa-crown text-amber-500 mr-1'></i>";
                                } elseif ($rank == 2) {
                                    $badgeClass = "bg-slate-200 text-slate-700 font-bold";
                                } elseif ($rank == 3) {
                                    $badgeClass = "bg-amber-700/10 text-amber-800 font-bold";
                                }
                    ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs <?php echo $badgeClass; ?>">
                                    <?php echo $icon . "#" . $rank; ?>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="text-[10px] text-slate-400">ID: #<?php echo isset($row['userId']) ? $row['userId'] : $rank; ?></div>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="py-4 px-6 text-center font-semibold text-slate-600">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                    <?php echo $row['total_attempt']; ?> Exams
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center font-black text-indigo-600 text-sm">
                                <?php echo $row['total_score']; ?> pts
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="user_details.php?id=<?php echo isset($row['userId']) ? $row['userId'] : ''; ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 rounded-xl font-bold transition text-[11px]">
                                    <i class="fa-solid fa-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                    <?php 
                            }
                        } else {
                    ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                No leaderboard performance data found for the selected criteria.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>