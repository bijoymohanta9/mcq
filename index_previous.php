<?php include 'inc/header.php'; ?>
<?php
Session::checkLogin();
?>

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden grid md:grid-cols-2 my-auto">
    
    <!-- Left Column: Branding / Illustration -->
    <div class="hidden md:flex flex-col justify-center items-center bg-gradient-to-br from-indigo-600 to-indigo-800 p-8 text-white relative">
        <div class="absolute inset-0 bg-blue-600 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative z-10 text-center">
            <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm border border-white/20">
                <i class="fa-solid fa-laptop-code text-4xl text-indigo-100"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Examination Portal</h2>
            <p class="text-indigo-200 text-sm max-w-xs leading-relaxed">Log in to access your dashboard, complete assessments, and review results.</p>
        </div>
    </div>

    <!-- Right Column: Login Form -->
    <div class="p-6 sm:p-10 flex flex-col justify-center">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">User Login</h2>
            <p class="text-slate-500 text-sm mt-1">Please enter your credentials to continue</p>
        </div>

        <form action="" method="post" class="space-y-4">
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Email Address</label>
                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
                    <span class="px-3.5 text-slate-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </span>
                    <input name="email" type="text" id="email" placeholder="example@domain.com"
                           class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Password</label>
                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
                    <span class="px-3.5 text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input name="password" type="password" id="password" placeholder="••••••••"
                           class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <input type="submit" id="loginsubmit" value="Login"
                       class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-sm cursor-pointer">
            </div>
        </form>

        <!-- New User Link -->
        <p class="text-center text-sm text-slate-500 mt-6">
            New User? <a href="register.php" class="font-semibold text-indigo-600 hover:underline">Signup Free</a>
        </p>

        <!-- Response Alert Spans -->
        <div class="mt-4 space-y-2">
            <div class="empty bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2.5 rounded-lg text-xs font-medium flex items-center gap-2" style="display: none;">
                <i class="fa-solid fa-circle-exclamation text-amber-500"></i> Field must not be empty!
            </div>
            <div class="error bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2.5 rounded-lg text-xs font-medium flex items-center gap-2" style="display: none;">
                <i class="fa-solid fa-circle-xmark text-rose-500"></i> Email or Password not matched!
            </div>
            <div class="disable bg-slate-100 border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-medium flex items-center gap-2" style="display: none;">
                <i class="fa-solid fa-ban text-slate-500"></i> User Id disabled!
            </div>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>