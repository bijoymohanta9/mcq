<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');

    // exam_history.php এর শুরুতে এই লাইনটি বসান
    $userId = Session::get("userid") ? Session::get("userid") : Session::get("userId");
    
    $exm = new Exam();
    //$userId = Session::get("userId"); // সেশন থেকে ইউজার আইডি
    
    $historyData = $exm->getUserExamHistory($userId);
?>

<div class="w-full max-w-5xl mx-auto my-8 px-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">আমার পরীক্ষার রেকর্ড</h1>
        <p class="text-slate-500 text-xs mt-1">আপনার দেওয়া সকল পরীক্ষার ফলাফল ও পারফরম্যান্স হিস্ট্রি</p>
    </div>

    <!-- History Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
    <thead>
        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
            <th class="py-3.5 px-4">ইউনিক আইডি</th>
            <th class="py-3.5 px-4">বিষয় ও ক্যাটাগরি</th>
            <th class="py-3.5 px-4">তারিখ ও সময়</th>
            <th class="py-3.5 px-4 text-center">স্কোর (প্রাপ্ত / মোট)</th>
            <th class="py-3.5 px-4 text-center">সাফল্যের হার</th>
            <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
            <th class="py-3.5 px-4 text-center">অ্যাকশন</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
        <?php 
        if ($historyData && $historyData->num_rows > 0) {
            while ($row = $historyData->fetch_assoc()) {
        ?>
            <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-4 font-mono font-bold text-indigo-600">
                    <?php echo $row['attempt_code']; ?>
                </td>
                <td class="py-4 px-4">
                    <div class="font-bold text-slate-800"><?php echo $row['subject_name']; ?></div>
                    <div class="text-[10px] text-slate-400"><?php echo $row['category_name']; ?></div>
                </td>
                <td class="py-4 px-4 text-slate-500">
                    <?php echo date('d M Y, h:i A', strtotime($row['exam_date'])); ?>
                </td>
                <td class="py-4 px-4 text-center font-semibold">
                    <span class="text-emerald-600"><?php echo $row['correct_answers']; ?></span> / <?php echo $row['total_questions']; ?>
                </td>
                <td class="py-4 px-4 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-xs <?php echo ($row['percentage'] >= 50) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'; ?>">
                        <?php echo $row['percentage']; ?>%
                    </span>
                </td>
                <td class="py-4 px-4 text-center">
                    <?php if($row['status'] == 'Passed'): ?>
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-emerald-100 text-emerald-800">উত্তীর্ণ</span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-lg bg-rose-100 text-rose-800">অনুত্তীর্ণ</span>
                    <?php endif; ?>
                </td>
                
                <!-- Action Button -->
                <td class="py-4 px-4 text-center">
                    <a href="viewans.php?code=<?php echo $row['attempt_code']; ?>" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white px-3 py-1.5 rounded-lg border border-indigo-200 transition font-medium text-[11px]">
                        <i class="fa-solid fa-eye"></i> বিস্তারিত
                    </a>
                </td>
            </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan='7' class='text-center py-8 text-slate-400'>আপনি এখনো কোনো পরীক্ষায় অংশ নেননি।</td></tr>";
        }
        ?>
    </tbody>
</table>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>