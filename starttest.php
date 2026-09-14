<?php include 'inc/header.php'; ?>
<?php
    Session::checkSession();

    // POST রেসপন্স প্রসেসিং
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_exam'])) {
        $category_id = (int)$_POST['category_id'];
        $num_questions = (int)$_POST['num_questions'];
        $time_limit = (int)$_POST['time_limit']; // মিনিটে

        // Exam Class এর মাধ্যমে র‍্যান্ডম প্রশ্নগুলো সেশনে সেভ করা
        $exm->setupCustomExam($category_id, $num_questions, $time_limit);
        
        header("Location: test.php?q=1");
        exit();
    }
?>

<div class="max-w-3xl mx-auto my-10 px-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 md:p-8">
        
        <!-- Header Section -->
        <div class="text-center border-b border-slate-100 pb-6 mb-6">
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight flex items-center justify-center gap-2">
                <i class="fa-solid fa-sliders text-indigo-600"></i>
                <span>Customize Your Exam</span>
            </h1>
            <p class="text-slate-500 text-xs md:text-sm mt-1">আপনার পছন্দ অনুযায়ী সাবজেক্ট, প্রশ্নের সংখ্যা ও সময় নির্ধারণ করুন</p>
        </div>

        <form action="" method="POST" class="space-y-6">
            
            <!-- Select Category/Subject -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-book text-indigo-500 mr-1"></i> সিলেক্ট সাবজেক্ট / ক্যাটাগরি
                </label>
                <select name="category_id" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold" required>
                    <option value="0">সকল সাবজেক্ট (Random All)</option>
                    <?php 
                        if (method_exists($exm, 'getCategories')) {
                            $getCats = $exm->getCategories();
                            if ($getCats) {
                                while ($cat = $getCats->fetch_assoc()) {
                                    $catId = $cat['id'] ?? $cat['catId'] ?? $cat['category_id'];
                                    $catName = $cat['category_name'] ?? $cat['catName'] ?? $cat['name'];
                                    echo "<option value='".$catId."'>".$catName."</option>";
                                }
                            }
                        }
                    ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Select Number of Questions -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-list-ol text-indigo-500 mr-1"></i> প্রশ্নের সংখ্যা
                    </label>
                    <select name="num_questions" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold">
                        <option value="5">৫টি প্রশ্ন</option>
                        <option value="10" selected>১০টি প্রশ্ন</option>
                        <option value="15">১৫টি প্রশ্ন</option>
                        <option value="20">২০টি প্রশ্ন</option>
                        <option value="25">২৫টি প্রশ্ন</option>
                    </select>
                </div>

                <!-- Select Time Limit -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-clock text-indigo-500 mr-1"></i> সময়সীমা (মিনিট)
                    </label>
                    <select name="time_limit" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold">
                        <option value="5">৫ মিনিট</option>
                        <option value="10" selected>১০ মিনিট</option>
                        <option value="15">১৫ মিনিট</option>
                        <option value="20">২০ মিনিট</option>
                        <option value="30">৩০ মিনিট</option>
                    </select>
                </div>
            </div>

            <!-- Start Exam Button -->
            <button type="submit" name="start_exam" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-play"></i>
                <span>পরীক্ষা শুরু করুন</span>
            </button>
        </form>

    </div>
</div>

<?php include 'inc/footer.php'; ?>