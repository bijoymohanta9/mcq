<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');
    $exm = new Exam();

    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-500"></i>
                <span>Categorywise Leaderboard</span>
            </h1>
            <p class="text-slate-500 text-xs mt-1">Find out the best performers for each exam category</p>
        </div>

        <!-- Category Filter Dropdown -->
        <form method="GET" action="" class="flex items-center gap-2">
            <select name="category_id" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-700 text-xs rounded-xl p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm outline-none font-semibold">
                <option value="0">-- Select Category --</option>
                <?php 
                    $getCats = $exm->getCategories(); // ধরে নেওয়া হচ্ছে ক্যাটাগরি রিটার্ন করে এমন মেথড রয়েছে
                    if ($getCats) {
                        while ($cat = $getCats->fetch_assoc()) {
                            $selected = ($category_id == $cat['id']) ? 'selected' : '';
                            echo "<option value='".$cat['id']."' $selected>".$cat['category_name']."</option>";
                        }
                    }
                ?>
            </select>
        </form>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6 w-20 text-center">Rank</th>
                        <th class="py-4 px-6">User Name</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6 text-center">Exams Attempted</th>
                        <th class="py-4 px-6 text-center">Total Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                        if ($category_id > 0) {
                            $getLeaderboard = $exm->getLeaderboardByCategory($category_id);
                            if ($getLeaderboard) {
                                $rank = 0;
                                while ($row = $getLeaderboard->fetch_assoc()) {
                                    $rank++;
                                    
                                    // Rank Badge Formatting
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
                            <td class="py-4 px-6 font-bold text-slate-800"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-4 px-6 text-slate-500"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="py-4 px-6 text-center font-semibold text-slate-600"><?php echo $row['total_attempt']; ?></td>
                            <td class="py-4 px-6 text-center font-black text-indigo-600 text-sm"><?php echo $row['total_score']; ?></td>
                        </tr>
                    <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='py-8 text-center text-slate-400 font-medium'>এই ক্যাটাগরিতে কোনো পারফরম্যান্স ডাটা পাওয়া যায়নি।</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='py-8 text-center text-slate-400 font-medium'>সেরা পারফরমার দেখতে অনুগ্রহ করে ওপরের ড্রপডাউন থেকে একটি ক্যাটাগরি সিলেক্ট করুন।</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>