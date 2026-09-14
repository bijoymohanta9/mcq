$(function() {

    // 1. User Registration
    $("#signupSubmit").click(function(e){
        e.preventDefault();

        var name     = $("#name").val();
        var username = $("#username").val();
        var password = $("#password").val();
        var email    = $("#email").val();

        $.ajax({
            type: "POST",
            url: "getregister.php",
            data: {
                name: name,
                username: username,
                password: password,
                email: email
            },
            success: function(data){
                $(".msg").html(data);
            }
        });
    });


    // 2. User Login
    $("#loginsubmit").click(function(e){
        e.preventDefault();

		console.log("Login button clicked!"); // Check if this appears in DevTools Console

        var email    = $("#email").val();
        var password = $("#password").val();
        
        $.ajax({
            type: "POST",
            url: "getlogin.php",
            data: {
                email: email, 
                password: password
            },
            success: function(response){
                var res = response.trim();
				console.log("Exact Response:", res);

                if(res == "empty"){
                    $(".empty").show();
                    $(".error").hide();
                    $(".disable").hide();
                } else if(res == "error"){
                    $(".error").show();
                    $(".empty").hide();
                    $(".disable").hide();
                } else if(res == "disable"){
                    $(".disable").show();
                    $(".empty").hide();
                    $(".error").hide();
                } else if(res == "success_exam"){
                    // অ্যাক্টিভ সাবস্ক্রিপশন থাকলে এক্সামে নিয়ে যাবে
                    window.location = "exam.php";
                } else if(res == "success_subscription"){
                    // সাবস্ক্রিপশন না থাকলে প্ল্যান কেনার পেজে নিয়ে যাবে
                    window.location = "subscription.php";
                }
            }
        });
    });
    
});