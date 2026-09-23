</main>

<!-- Modern SaaS Footer -->
<footer class="bg-white border-t border-slate-200/80 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            
            <!-- Left: Copyright & System Status -->
            <div class="flex items-center space-x-3">
                <p>&copy; <?php echo date("Y"); ?> <span class="font-semibold text-slate-700">ExamPrep</span>. All rights reserved.</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Systems Operational
                </span>
            </div>

            <!-- Center: Quick Policy Links -->
            <div class="flex items-center space-x-4 font-medium text-slate-600">
                <button onclick="openModal('privacyModal')" class="hover:text-indigo-600 transition focus:outline-none">প্রাইভেসি পলিসি</button>
                <span>&bull;</span>
                <button onclick="openModal('termsModal')" class="hover:text-indigo-600 transition focus:outline-none">ব্যবহারের শর্তাবলী</button>
                <span>&bull;</span>
                <button onclick="openModal('supportModal')" class="hover:text-indigo-600 transition focus:outline-none">হেল্প ও সাপোর্ট</button>
            </div>

            <!-- Right: Developer Branding -->
            <div class="flex items-center space-x-1 font-medium">
                <span>Developed by</span>
                <a href="https://vfctechnologies.com" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 font-semibold transition hover:underline">
                    VFC Technologies
                </a>
            </div>

        </div>
    </div>
</footer>

<!-- ================= MODALS ================= -->

<!-- Privacy Policy Modal -->
<div id="privacyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative animate-fadeIn">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-600"></i> প্রাইভেসি পলিসি
            </h3>
            <button onclick="closeModal('privacyModal')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="py-4 text-xs text-slate-600 space-y-3 max-h-[60vh] overflow-y-auto leading-relaxed">
            <p>আপনার ব্যক্তিগত তথ্যের সুরক্ষা দেওয়া আমাদের অগ্রাধিকার। এই পলিসিতে ব্যাখ্যা করা হয়েছে আমরা কীভাবে আপনার তথ্য সংগ্রহ ও ব্যবহার করি:</p>
            <ul class="list-disc pl-5 space-y-1 text-slate-700">
                <li><strong>তথ্য সংগ্রহ:</strong> অ্যাকাউন্ট তৈরির সময় নাম, ইমেইল ও ফোন নম্বর সংগ্রহ করা হয়।</li>
                <li><strong>ব্যবহার:</strong> পরীক্ষার ফলাফল ট্র্যাকিং, অ্যাকাউন্ট যাচাইকরণ এবং সাবস্ক্রিপশন সেবার জন্য তথ্য ব্যবহূত হয়।</li>
                <li><strong>নিরাপত্তা:</strong> আমরা এনক্রিপশন প্রটোকল ব্যবহার করি যাতে আপনার তথ্য অন্য কারো কাছে প্রকাশ না পায়।</li>
            </ul>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button onclick="closeModal('privacyModal')" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs rounded-xl hover:bg-indigo-700 transition">বন্ধ করুন</button>
        </div>
    </div>
</div>

<!-- Terms of Service Modal -->
<div id="termsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative animate-fadeIn">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-file-contract text-indigo-600"></i> ব্যবহারের শর্তাবলী
            </h3>
            <button onclick="closeModal('termsModal')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="py-4 text-xs text-slate-600 space-y-3 max-h-[60vh] overflow-y-auto leading-relaxed">
            <p>ExamPrep প্ল্যাটফর্মটি ব্যবহার করার পূর্বে অনুগ্রহ করে নিয়মাবলী পড়ে নিন:</p>
            <ul class="list-disc pl-5 space-y-1 text-slate-700">
                <li><strong>অ্যাকাউন্ট শেয়ারিং:</strong> একটি অ্যাকাউন্ট শুধুমাত্র একক ব্যক্তির ব্যবহারের জন্য। শেয়ার করা নিষিদ্ধ।</li>
                <li><strong>পেমেন্ট ও রিফান্ড:</strong> সাবস্ক্রিপশন ফি নির্দিষ্ট মেয়াদের জন্য প্রযোজ্য এবং সাধারণত অফেরতযোগ্য।</li>
                <li><strong>সঠিক তথ্য:</strong> অ্যাকাউন্টে সঠিক তথ্য প্রদান করা বাধ্যতামূলক।</li>
            </ul>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button onclick="closeModal('termsModal')" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs rounded-xl hover:bg-indigo-700 transition">আমি সম্মত</button>
        </div>
    </div>
</div>

<!-- Help & Support Modal -->
<div id="supportModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative animate-fadeIn">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-headset text-indigo-600"></i> হেল্প ও সাপোর্ট
            </h3>
            <button onclick="closeModal('supportModal')" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="py-4 text-xs text-slate-600 space-y-3 max-h-[60vh] overflow-y-auto leading-relaxed">
            <p>আপনার কোনো প্রশ্ন, সমস্যা বা পেমেন্ট সংক্রান্ত তথ্য জানতে নিচের যেকোনো মাধ্যমে যোগাযোগ করুন:</p>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                <p class="flex items-center gap-2"><i class="fa-solid fa-envelope text-indigo-500"></i> <strong>ইমেইল:</strong> support@examprep.com</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-phone text-indigo-500"></i> <strong>হটলাইন:</strong> +৮৮০ ১৭০০-০০০০০০</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-clock text-indigo-500"></i> <strong>সময়সূচী:</strong> সকাল ১০:০০ - রাত ৮:০০ (প্রতিদিন)</p>
            </div>
        </div>
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button onclick="closeModal('supportModal')" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs rounded-xl hover:bg-indigo-700 transition">ঠিক আছে</button>
        </div>
    </div>
</div>

<!-- Modal Control Script -->
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Close modal when clicking outside the content box
    window.addEventListener('click', function(e) {
        ['privacyModal', 'termsModal', 'supportModal'].forEach(id => {
            const modal = document.getElementById(id);
            if (e.target === modal) {
                closeModal(id);
            }
        });
    });
</script>

</body>
</html>