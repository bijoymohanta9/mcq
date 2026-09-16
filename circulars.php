<?php 
    include_once 'inc/header.php'; // আপনার ক্লায়েন্ট ফ্রন্টএন্ড হেডার
    include_once 'classes/Exam.php';
    $exm = new Exam();

    // ক্যাটাগরি ফিল্টার চেক
    $selected_cat = isset($_GET['category']) ? (int)$_GET['category'] : null;
    $getCirculars = $exm->getActiveCirculars($selected_cat);
?>

<div class="w-full max-w-6xl mx-auto my-10 px-4 space-y-8">

    <!-- Header Section -->
    <div class="text-center space-y-2">
        <span class="px-3 py-1 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full border border-indigo-100">সরকারি চাকরি পোর্টালে স্বাগতম</span>
        <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">চলমান সরকারি জব সার্কুলারসমূহ</h1>
        <p class="text-xs text-slate-500 max-w-xl mx-auto">ক্যাটাগরি অনুযায়ী সরকারি নিয়োগ বিজ্ঞপ্তিগুলো দেখুন এবং সরাসরি অফিসিয়াল ওয়েবসাইটে গিয়ে আবেদন সম্পন্ন করুন।</p>
    </div>

    <!-- Category Navigation / Filter Tabs -->
    <div class="flex items-center justify-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <a href="circulars.php" class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap <?php echo !$selected_cat ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'; ?>">
            সকল সার্কুলার
        </a>
        <?php 
            $getCats = $exm->getCategories(); // আপনার ক্যাটাগরি লিস্ট ফেচিং মেথড
            if ($getCats) {
                while ($cat = $getCats->fetch_assoc()) {
                    $isActive = ($selected_cat == $cat['id']);
                    $activeClass = $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50';
                    echo "<a href='circulars.php?category={$cat['id']}' class='px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap {$activeClass}'>{$cat['category_name']}</a>";
                }
            }
        ?>
    </div>

    <!-- Job Circular Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
            if ($getCirculars) {
                while ($row = $getCirculars->fetch_assoc()) {
                    $formatted_deadline = date("d M, Y", strtotime($row['deadline']));
        ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <!-- Category Badge & Deadline -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-1 text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-lg">
                            <?php echo $row['category_name']; ?>
                        </span>
                        <span class="text-[11px] font-semibold text-rose-600 flex items-center gap-1 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                            <i class="fa-regular fa-clock"></i> শেষ: <?php echo $formatted_deadline; ?>
                        </span>
                    </div>

                    <!-- Title & Organization -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 line-clamp-2 hover:text-indigo-600 transition">
                            <?php echo $row['title']; ?>
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-building text-slate-400"></i>
                            <span><?php echo $row['organization']; ?></span>
                        </p>
                    </div>

                    <!-- Details Box -->
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-center justify-between text-xs">
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase">মোট পদসংখ্যা</span>
                            <span class="font-bold text-slate-700"><?php echo !empty($row['vacancy']) ? $row['vacancy'] : 'বিজ্ঞপ্তি দেখুন'; ?></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-slate-400 font-bold uppercase">আবেদনের মাধ্যম</span>
                            <span class="font-bold text-slate-700">অনলাইন পোর্টালে</span>
                        </div>
                    </div>
                </div>

                <!-- Action Links -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <?php if (!empty($row['pdf_url'])): ?>
                        <a href="<?php echo $row['pdf_url']; ?>" target="_blank" rel="noopener noreferrer" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i>
                            <span>PDF নোটিশ</span>
                        </a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>

                    <!-- Real External Application Link -->
                    <a href="<?php echo $row['apply_url']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center gap-2">
                        <span>আবেদন করুন</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>
        <?php 
                }
            } else {
        ?>
            <!-- Empty State -->
            <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-12 text-center space-y-3">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto text-xl">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">কোনো রানিং সার্কুলার পাওয়া যায়নি</h3>
                <p class="text-xs text-slate-400">বর্তমানে এই ক্যাটাগরিতে সক্রিয় কোনো আবেদনের বিজ্ঞপ্তি নেই।</p>
            </div>
        <?php } ?>
    </div>

</div>

<?php include_once 'inc/footer.php'; ?>