<?php include 'inc/header.php'; ?>

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden grid md:grid-cols-2 my-auto">
    
    <!-- Left Column: Branding / Illustration -->
    <div class="hidden md:flex flex-col justify-center items-center bg-gradient-to-br from-indigo-600 to-indigo-800 p-8 text-white relative">
        <div class="absolute inset-0 bg-blue-600 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative z-10 text-center">
            <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm border border-white/20">
                <i class="fa-solid fa-user-plus text-4xl text-indigo-100"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Create an Account</h2>
            <p class="text-indigo-200 text-sm max-w-xs leading-relaxed">Join our platform today to take online exams, evaluate your skills, and view results instanty.</p>
        </div>
    </div>

    <!-- Right Column: Registration Form -->
    <div class="p-6 sm:p-10 flex flex-col justify-center">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">User Registration</h2>
            <p class="text-slate-500 text-sm mt-1">Fill in your details to create a free account</p>
        </div>

        <form action="" method="post" class="space-y-3.5">
            <!-- Full Name Input -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Full Name</label>
                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
                    <span class="px-3.5 text-slate-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </span>
                    <input name="name" type="text" id="name" placeholder="John Doe"
                           class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
                </div>
            </div>

            <!-- Username Input -->
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Username</label>
                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
                    <span class="px-3.5 text-slate-400">
                        <i class="fa-solid fa-at text-sm"></i>
                    </span>
                    <input name="username" type="text" id="username" placeholder="johndoe"
                           class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Password</label>
                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
                    <span class="px-3.5 text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input name="password" type="password" id="password" placeholder="••••••••"
                           class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
                </div>
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Email Address</label>
                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
                    <span class="px-3.5 text-slate-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </span>
                    <input name="email" type="text" id="email" placeholder="example@domain.com"
                           class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <input type="submit" id="signupSubmit" value="Sign Up"
                       class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-sm cursor-pointer">
            </div>
        </form>

        <!-- Login Redirect Link -->
        <p class="text-center text-sm text-slate-500 mt-5">
            Already Registered? <a href="index.php" class="font-semibold text-indigo-600 hover:underline">Login Here</a>
        </p>

        <!-- Response Alert Container (Preserved .msg span/div for AJAX response) -->
        <div class="mt-4">
            <span class="msg block text-xs rounded-lg font-medium"></span>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>