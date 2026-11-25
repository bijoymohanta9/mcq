<?php 
    include_once 'inc/header.php'; // আপনার ক্লায়েন্ট ফ্রন্টএন্ড হেডার
    include_once 'classes/Exam.php';
    $exm = new Exam();

    // ক্যাটাগরি ফিল্টার চেক
    $selected_cat = isset($_GET['category']) ? (int)$_GET['category'] : null;
    $getCirculars = $exm->getActiveCirculars($selected_cat);
    
    // আপনার হোয়াটসঅ্যাপ নম্বর (স্পেস বা + ছাড়া)
    $whatsapp_no = "8801954819285";
?>

<div class="w-full max-w-6xl mx-auto my-10 px-4 space-y-8">

    <!-- Header Section -->
    <div class="text-center space-y-2">
        <span class="px-3 py-1 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full border border-indigo-100">সরকারি চাকরি পোর্টালে স্বাগতম</span>
        <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">চলমান সরকারি জব সার্কুলারসমূহ</h1>
        <p class="text-xs text-slate-500 max-w-xl mx-auto">ক্যাটাগরি অনুযায়ী সরকারি নিয়োগ বিজ্ঞপ্তিগুলো দেখুন এবং সরাসরি অফিসিয়াল ওয়েবসাইটে গিয়ে অথবা আমাদের মাধ্যমে আবেদন সম্পন্ন করুন।</p>
    </div>

    <!-- Category Navigation / Filter Tabs -->
    <div class="flex items-center justify-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <a href="circulars.php" class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap <?php echo !$selected_cat ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'; ?>">
            সকল সার্কুলার
        </a>
        <?php 
            $getCats = $exm->getCategories();
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
                    // সার্কুলারের টাইটেল নিরাপদভাবে JS-এ পাঠানোর জন্য
                    $safe_title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
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
                <div class="pt-3 border-t border-slate-100 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
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

                    <!-- New Friendly A-to-Z WhatsApp Application Button -->
                    <button type="button" 
                            onclick="openApplyHelpModal('<?php echo $safe_title; ?>')"
                            class="w-full py-2.5 px-3 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-base text-emerald-600"></i>
                        <span>আবেদন সহায়তা নিন (A-Z সাপোর্ট)</span>
                    </button>
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

<!-- WhatsApp A-to-Z Support & Payment Info Modal -->
<div id="applyHelpModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4 relative animate-fade-in">
        
        <!-- Close Button -->
        <button onclick="closeApplyHelpModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">A-to-Z আবেদন সহায়তা</h3>
                <p class="text-xs text-slate-500">কোনো ঝামেলা ছাড়াই নির্ভুলভাবে আবেদন করুন</p>
            </div>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
            <span class="text-[10px] font-bold text-slate-400 uppercase block">নির্বাচিত সার্কুলার:</span>
            <p id="modalCircularTitle" class="text-xs font-bold text-indigo-700 mt-0.5"></p>
        </div>

        <!-- Step-by-Step & Payment Process -->
        <div class="space-y-2.5 text-xs text-slate-600">
            <p class="font-bold text-slate-800">কীভাবে আবেদন ও পেমেন্ট করবেন?</p>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">১</span>
                    <span>নিচের বাটনে ক্লিক করে WhatsApp-এ আপনার <b>ছবি, স্বাক্ষর ও শিক্ষাগত তথ্য</b> পাঠান।</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">২</span>
                    <span>আমরা ফর্ম পূরণ করে আপনাকে <b>Draft Copy</b> দেখাবো। সব তথ্য ঠিক থাকলে পেমেন্ট করবেন।</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">৩</span>
                    <span><b>পেমেন্ট পদ্ধতি:</b> সরকারি আবেদন ফি + সামান্য সার্ভিস চার্জ আমাদের <b>bKash / Nagad</b> নম্বরে পাঠাবেন। পেমেন্টের পরপরই ফাইনাল <b>Applicant Copy</b> পেয়ে যাবেন।</span>
                </li>
            </ul>
        </div>

        <!-- Direct WhatsApp Redirect Button -->
        <div class="pt-2">
            <a id="whatsappDirectLink" href="#" target="_blank" rel="noopener noreferrer" 
               class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-base"></i>
                <span>WhatsApp-এ মেসেজ পাঠান (+880 1954-819285)</span>
            </a>
        </div>
    </div>
</div>

<script>
    function openApplyHelpModal(circularTitle) {
        const whatsappNumber = "<?php echo $whatsapp_no; ?>";
        document.getElementById('modalCircularTitle').innerText = circularTitle;
        
        // Pre-filled message for WhatsApp
        const message = `আসসালামু আলাইকুম, আমি '${circularTitle}' সার্কুলারে আপনাদের মাধ্যমে বন্ধু হিসেবে A-to-Z আবেদন করতে চাই। কী কী তথ্য লাগবে এবং পেমেন্ট প্রসেসটি জানাবেন।`;
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
        
        document.getElementById('whatsappDirectLink').href = whatsappUrl;
        document.getElementById('applyHelpModal').classList.remove('hidden');
    }

    function closeApplyHelpModal() {
        document.getElementById('applyHelpModal').classList.add('hidden');
    }

    // পপ-আপের বাইরে ক্লিক করলে বন্ধ হওয়ার জন্য
    document.getElementById('applyHelpModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApplyHelpModal();
        }
    });
</script>

<?php include_once 'inc/footer.php'; ?>