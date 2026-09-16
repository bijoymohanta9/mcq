<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include_once '../classes/Exam.php';
    $exm = new Exam();

    // সার্কুলার সাবমিট হ্যান্ডলার
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_circular'])) {
        $msg = $exm->addCircular($_POST);
        $_SESSION['msg'] = $msg;
        header("Location: circular_add.php");
        exit();
    }

    include_once 'inc/header.php';

    $msg = '';
    if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4 space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">নতুন জব সার্কুলার যুক্ত করুন</h1>
            <p class="text-slate-500 text-xs mt-1">সরকারি চাকরির বিজ্ঞপ্তি সাইটে প্রকাশ করার জন্য ফর্মটি পূরণ করুন</p>
        </div>
        <a href="circular_list.php" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-list"></i>
            <span>সকল সার্কুলার দেখুন</span>
        </a>
    </div>

    <!-- Alert Message -->
    <?php if (!empty($msg)) echo $msg; ?>

    <!-- Form Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 md:p-8">
        <form action="circular_add.php" method="post" class="space-y-6">
            <input type="hidden" name="add_circular" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Category Select -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        সরকারি ক্যাটাগরি <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        <option value="">-- ক্যাটাগরি সিলেক্ট করুন --</option>
                        <?php 
                            $getCats = $exm->getCategories();
                            if ($getCats) {
                                while ($cat = $getCats->fetch_assoc()) {
                                    echo "<option value='{$cat['id']}'>{$cat['category_name']}</option>";
                                }
                            }
                        ?>
                    </select>
                </div>

                <!-- Deadline -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        আবেদনের শেষ তারিখ (Deadline) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="deadline" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    বিজ্ঞপ্তির শিরোনাম (Title) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" placeholder="যেমন: ৪৬তম বিসিএস পরীক্ষার বিজ্ঞপ্তি-২০২৪" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Organization -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        প্রতিষ্ঠানের নাম (Organization) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="organization" placeholder="যেমন: বাংলাদেশ সরকারি কর্ম কমিশন (BPSC)" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>

                <!-- Vacancy -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        পদসংখ্যা (Vacancy)
                    </label>
                    <input type="text" name="vacancy" placeholder="যেমন: ৩,১৪০ জন" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Apply URL -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    আবেদন করার ডিরেক্ট লিংক (Apply URL) <span class="text-rose-500">*</span>
                </label>
                <input type="url" name="apply_url" placeholder="https://bpsc.teletalk.com.bd" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
            </div>

            <!-- PDF Notice URL -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    সার্কুলার PDF লিংক (Optional PDF URL)
                </label>
                <input type="url" name="pdf_url" placeholder="https://example.com/circular.pdf" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition font-mono">
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>সার্কুলার প্রকাশ করুন</span>
                </button>
            </div>

        </form>
    </div>
</div>

<?php include 'inc/footer.php'; ?>