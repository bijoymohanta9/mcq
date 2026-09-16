<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include_once '../classes/Exam.php';
    $exm = new Exam();

    // সার্কুলার ডিলিট করার হ্যান্ডলার
    if (isset($_GET['del_id'])) {
        $del_id = (int)$_GET['del_id'];
        $delCircular = $exm->deleteCircular($del_id);
        $_SESSION['msg'] = $delCircular;
        header("Location: circular_list.php");
        exit();
    }

    include_once 'inc/header.php';

    $msg = '';
    if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4 space-y-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">সার্কুলার তালিকা</h1>
            <p class="text-slate-500 text-xs mt-1">আপনার সিস্টেমে থাকা সকল জব সার্কুলারের তালিকা</p>
        </div>
        <a href="circular_add.php" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>নতুন সার্কুলার</span>
        </a>
    </div>

    <!-- Alert Message -->
    <?php if (!empty($msg)) echo $msg; ?>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">ক্যাটাগরি</th>
                        <th class="px-4 py-3">শিরোনাম ও প্রতিষ্ঠান</th>
                        <th class="px-4 py-3">পদসংখ্যা</th>
                        <th class="px-4 py-3">শেষ তারিখ</th>
                        <th class="px-4 py-3 text-center">আবেদন লিংক</th>
                        <th class="px-4 py-3 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                        $getAll = $exm->getAllCirculars(); // সকল সার্কুলার ফেচ করার মেথড
                        if ($getAll) {
                            while ($row = $getAll->fetch_assoc()) {
                                $isExpired = strtotime($row['deadline']) < strtotime(date('Y-m-d'));
                    ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                <?php echo $row['category_name']; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800"><?php echo $row['title']; ?></div>
                                <div class="text-[11px] text-slate-400"><?php echo $row['organization']; ?></div>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <?php echo !empty($row['vacancy']) ? $row['vacancy'] : '-'; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-[11px] font-bold rounded-lg <?php echo $isExpired ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                                    <?php echo date("d M, Y", strtotime($row['deadline'])); ?>
                                    <?php if ($isExpired) echo ' (মেয়াদ শেষ)'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="<?php echo $row['apply_url']; ?>" target="_blank" class="text-indigo-600 hover:underline font-bold text-[11px]">
                                    লিংক দেখুন <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="circular_list.php?del_id=<?php echo $row['id']; ?>" onclick="return confirm('আপনি কি নিশ্চিত যে এই সার্কুলারটি মুছে ফেলতে চান?');" class="px-2.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' class='px-4 py-8 text-center text-slate-400'>কোনো সার্কুলার পাওয়া যায়নি।</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>