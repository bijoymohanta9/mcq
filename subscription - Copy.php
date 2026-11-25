<?php 
    include 'inc/header.php'; 
    Session::checkSession();
    include_once 'classes/Exam.php';
    $exm = new Exam();
?>

<div class="w-full max-w-5xl mx-auto my-6 px-4">
    
    <!-- Page Header & Instructions -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-slate-900 tracking-tight">নিচের ২ টি ধাপে পরীক্ষা ও সাবস্ক্রিপশন প্ল্যান নির্বাচন করুন</h2>
        <p class="text-slate-600 text-sm mt-2 max-w-xl mx-auto">
            আপনার কাঙ্ক্ষিত পরীক্ষার কোর্সটি বেছে নিন এবং মেয়াদ সিলেক্ট করে সাবস্ক্রিপশন সম্পন্ন করুন। পেমেন্ট সম্পন্ন হওয়ার সাথে সাথেই সকল মডেল টেস্ট একটিভ হয়ে যাবে।
        </p>
    </div>

    <form id="subscriptionForm" class="space-y-8">
        
        <!-- Step 1: Select Exam Category -->
        <div>
            <div class="flex items-center space-x-2 mb-4">
                <span class="w-7 h-7 bg-indigo-600 text-white font-bold rounded-full flex items-center justify-center text-sm">১</span>
                <h3 class="text-lg font-bold text-slate-800">পরীক্ষা বা সার্কুলার নির্বাচন করুন</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                <?php 
                    $getCats = $exm->getCategories();
                    if ($getCats) {
                        $i = 0;
                        $icons  = ['fa-book-bookmark', 'fa-building-columns', 'fa-graduation-cap', 'fa-user-tie', 'fa-gear'];
                        $colors = [
                            ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'hover' => 'group-hover:bg-indigo-600'],
                            ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'hover' => 'group-hover:bg-emerald-600'],
                            ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'hover' => 'group-hover:bg-amber-600']
                        ];

                        while ($cat = $getCats->fetch_assoc()) {
                            $icon  = $icons[$i % count($icons)];
                            $color = $colors[$i % count($colors)];
                            $i++;
                ?>
                    <!-- Dynamic Category Card -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="category_id" value="<?php echo $cat['id']; ?>" class="peer sr-only" required <?php if($i == 1) echo 'checked'; ?>>
                        <div class="p-6 bg-white border-2 border-slate-200 rounded-2xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50/40 peer-checked:shadow-md transition-all h-full flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 <?php echo $color['bg']; ?> <?php echo $color['text']; ?> rounded-xl flex items-center justify-center mb-4 text-xl <?php echo $color['hover']; ?> group-hover:text-white transition">
                                    <i class="fa-solid <?php echo $icon; ?>"></i>
                                </div>
                                <h4 class="font-bold text-slate-800 text-lg mb-1"><?php echo htmlspecialchars($cat['category_name']); ?></h4>
                                <p class="text-slate-500 text-xs leading-relaxed"><?php echo htmlspecialchars($cat['category_name']); ?> সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট।</p>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-xs <?php echo $color['text']; ?> font-semibold">
                                <span>বিষয়ভিত্তিক পরীক্ষা</span>
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </div>
                        </div>
                    </label>
                <?php 
                        }
                    } else {
                        echo "<p class='text-slate-500 text-sm col-span-3 text-center'>কোনো ক্যাটাগরি পাওয়া যায়নি!</p>";
                    }
                ?>
            </div>
        </div>

        <!-- Step 2: Select Duration & View Price -->
        <div>
            <div class="flex items-center space-x-2 mb-4">
                <span class="w-7 h-7 bg-indigo-600 text-white font-bold rounded-full flex items-center justify-center text-sm">২</span>
                <h3 class="text-lg font-bold text-slate-800">সাবস্ক্রিপশনের মেয়াদ সিলেক্ট করুন</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                
                <!-- 3 Months -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="duration" value="3_months" data-price="500" class="peer sr-only duration-radio" required>
                    <div class="p-5 bg-white border border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50/20 peer-checked:ring-2 peer-checked:ring-indigo-600 transition text-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">ট্রায়াল প্যাক</span>
                        <h5 class="text-xl font-bold text-slate-800">৩ মাস</h5>
                        <p class="text-2xl font-black text-indigo-600 my-2">৳ ৫০০</p>
                        <span class="text-xs text-slate-400">প্রতি মাসে ৳ ১৬৬</span>
                    </div>
                </label>

                <!-- 6 Months -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="duration" value="6_months" data-price="900" class="peer sr-only duration-radio" checked>
                    <div class="p-5 bg-white border border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50/20 peer-checked:ring-2 peer-checked:ring-indigo-600 transition text-center relative overflow-hidden">
                        <span class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-bl-lg">পপুলার</span>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">স্ট্যান্ডার্ড</span>
                        <h5 class="text-xl font-bold text-slate-800">৬ মাস</h5>
                        <p class="text-2xl font-black text-indigo-600 my-2">৳ ৯০০</p>
                        <span class="text-xs text-slate-400">প্রতি মাসে ৳ ১৫০</span>
                    </div>
                </label>

                <!-- 1 Year -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="duration" value="1_year" data-price="1500" class="peer sr-only duration-radio">
                    <div class="p-5 bg-white border border-slate-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50/20 peer-checked:ring-2 peer-checked:ring-indigo-600 transition text-center relative overflow-hidden">
                        <span class="absolute top-0 right-0 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-bl-lg">সেরা ছাড়</span>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">প্রিমিয়াম প্যাক</span>
                        <h5 class="text-xl font-bold text-slate-800">১ বছর (১২ মাস)</h5>
                        <p class="text-2xl font-black text-indigo-600 my-2">৳ ১,৫০০</p>
                        <span class="text-xs text-slate-400">প্রতি মাসে ৳ ১২৫</span>
                    </div>
                </label>

            </div>
        </div>

        <!-- Billing Summary & Submit Section -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-1 text-center md:text-left">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">মোট পরিশোধযোগ্য বিল</span>
                <div class="text-3xl font-black text-slate-900 flex items-center justify-center md:justify-start gap-1">
                    <span>৳</span> <span id="totalAmount">৯০০</span> <span class="text-xs font-normal text-slate-500">(সকল ট্যাক্স সহ)</span>
                </div>
            </div>

            <button type="submit" id="payButton" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg hover:shadow-xl transition flex items-center justify-center space-x-2 text-base">
                <span>পেমেন্ট করুন</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
        </div>

    </form>

</div>

<!-- Dynamic Price Sync & Redirection Script -->
<script>
    $(document).ready(function() {
        // Localized Bengali numerals helper function
        function toBengaliNumber(n) {
            const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return n.toString().replace(/\d/g, d => banglaDigits[d]);
        }

        // 1. Sync price dynamically on initial page load (fixes 6-month default selection sync)
        var initialPrice = $(".duration-radio:checked").data('price');
        if (initialPrice) {
            $('#totalAmount').text(toBengaliNumber(initialPrice));
        }

        // 2. Update price on duration radio change
        $('.duration-radio').change(function() {
            var price = $(this).data('price');
            $('#totalAmount').text(toBengaliNumber(price));
        });

        // 3. Handle form submission & redirect to checkout.php
        $("#subscriptionForm").submit(function(e){
            e.preventDefault();

            var selectedCategory = $("input[name='category_id']:checked").val();
            var selectedPlan     = $("input[name='duration']:checked").val();
            var amount           = $("input[name='duration']:checked").data('price');

            if (!selectedCategory) {
                alert("অনুগ্রহ করে একটি পরীক্ষা বা ক্যাটাগরি নির্বাচন করুন।");
                return false;
            }

            if (!selectedPlan) {
                alert("অনুগ্রহ করে সাবস্ক্রিপশনের মেয়াদ সিলেক্ট করুন।");
                return false;
            }

            var checkoutUrl = "checkout.php?category_id=" + encodeURIComponent(selectedCategory) + 
                              "&plan=" + encodeURIComponent(selectedPlan) + 
                              "&amount=" + encodeURIComponent(amount);

            window.location.href = checkoutUrl;
        });
    });
</script>

<?php include 'inc/footer.php'; ?>