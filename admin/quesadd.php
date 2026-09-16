<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Class file include
    include_once '../classes/Exam.php';
    $exm = new Exam();

    // ১. সিঙ্গেল প্রশ্ন ম্যানুয়ালি যুক্ত করার হ্যান্ডলার
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['single_add'])) {
        $addQue = $exm->addQuestions($_POST);
        $_SESSION['msg'] = $addQue;
        
        header("Location: quesadd.php");
        exit();
    }

    // ২. বাল্ক CSV/Excel ইম্পোর্ট করার হ্যান্ডলার
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_import'])) {
        $importStatus = $exm->importBulkQuestions($_FILES);
        $_SESSION['msg'] = $importStatus;
        
        header("Location: quesadd.php");
        exit();
    }

    // Header লোড
    include_once 'inc/header.php';

    // ডায়নামিক প্রশ্ন নম্বর নির্ধারণ
    $total = $exm->getTotalRows();
    $next  = $total ? ($total + 1) : 1;

    // সেশন মেসেজ রিড করা
    $msg = '';
    if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4 space-y-8">
    
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">নতুন প্রশ্ন যুক্ত করুন</h1>
        <p class="text-slate-500 text-xs mt-1">ম্যানুয়ালি ফরম পুরণ করে অথবা CSV ফাইল আপলোড করে একসাথে একাধিক প্রশ্ন এন্ট্রি দিন</p>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($msg)): ?>
        <div>
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <!-- Bulk CSV/Excel Import Section -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-slate-50 border border-emerald-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/10 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-file-csv text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">বাল্ক ইম্পোর্ট (Bulk CSV Import)</h2>
                    <p class="text-[11px] text-slate-500">Excel থেকে `.csv` ফরম্যাটে সেভ করে একসাথে একাধিক প্রশ্ন ইম্পোর্ট করুন</p>
                </div>
            </div>
        </div>

        <form action="quesadd.php" method="post" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-3">
            <input type="file" name="csv_file" accept=".csv" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 transition cursor-pointer bg-white rounded-xl border border-slate-200 p-1">
            <button type="submit" name="bulk_import" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-file-import"></i>
                <span>ইম্পোর্ট করুন</span>
            </button>
        </form>
    </div>

    <!-- Manual Form Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 md:p-8">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-800">ম্যানুয়াল প্রশ্ন এন্ট্রি</h2>
        </div>

        <form action="quesadd.php" method="post" class="space-y-6">
            <input type="hidden" name="single_add" value="1">
            
            <!-- Category, Subject & Question No. (Grid Layout) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                <!-- Exam Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        পরীক্ষার ধরন (Category) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="category_id" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition appearance-none">
                            <option value="">-- ক্যাটাগরি সিলেক্ট করুন --</option>
                            <?php 
                                $query = "SELECT * FROM tbl_category ORDER BY category_name ASC";
                                $getCats = $db->select($query);
                                if ($getCats) {
                                    while ($cat = $getCats->fetch_assoc()) {
                                        echo "<option value='{$cat['id']}'>{$cat['category_name']}</option>";
                                    }
                                }
                            ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        বিষয় (Subject) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="subject_id" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-indigo-500 outline-none transition appearance-none">
                            <option value="">-- বিষয় সিলেক্ট করুন --</option>
                            <?php 
                                $query = "SELECT * FROM tbl_subject ORDER BY subject_name ASC";
                                $getSubs = $db->select($query);
                                if ($getSubs) {
                                    while ($sub = $getSubs->fetch_assoc()) {
                                        echo "<option value='{$sub['id']}'>{$sub['subject_name']}</option>";
                                    }
                                }
                            ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Question No -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        প্রশ্ন নম্বর (Question No)
                    </label>
                    <input type="number" name="quesNo" value="<?php echo $next; ?>" readonly class="w-full bg-slate-100 border border-slate-200 text-slate-500 text-xs font-bold rounded-xl px-3.5 py-3 cursor-not-allowed outline-none">
                </div>
            </div>

            <hr class="border-slate-100 my-2">

            <!-- Question Text -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    প্রশ্ন (Question Title) <span class="text-rose-500">*</span>
                </label>
                <textarea name="ques" rows="3" placeholder="প্রশ্নটি লিখুন..." required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl p-3.5 focus:ring-2 focus:ring-indigo-500 outline-none transition"></textarea>
            </div>

            <!-- Choices Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">অপশন ১ (Choice One)</label>
                    <input type="text" name="ans1" placeholder="প্রথম বিকল্প..." required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">অপশন ২ (Choice Two)</label>
                    <input type="text" name="ans2" placeholder="দ্বিতীয় বিকল্প..." required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">অপশন ৩ (Choice Three)</label>
                    <input type="text" name="ans3" placeholder="তৃতীয় বিকল্প..." required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">অপশন ৪ (Choice Four)</label>
                    <input type="text" name="ans4" placeholder="চতুর্থ বিকল্প..." required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Right Answer Selection -->
            <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4">
                <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">
                    সঠিক উত্তর নম্বর (Correct Answer Number) <span class="text-rose-500">*</span>
                </label>
                <select name="rightAns" required class="w-full md:w-1/2 bg-white border border-indigo-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    <option value="">-- সঠিক অপশন নম্বর নির্বাচন করুন --</option>
                    <option value="1">অপশন ১ (Choice One)</option>
                    <option value="2">Option 2 (Choice Two)</option>
                    <option value="3">অপশন ৩ (Choice Three)</option>
                    <option value="4">অপশন ৪ (Choice Four)</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus-circle text-sm"></i>
                    <span>প্রশ্নটি যোগ করুন</span>
                </button>
            </div>

        </form>
    </div>

</div>

<?php include 'inc/footer.php'; ?>