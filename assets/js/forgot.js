$(document).ready(function () {

	$("#progress").hide();

    var $submitButton = $("#submit");
    $submitButton.click(function (e) {

        e.preventDefault();
        var email = $('#email');

        $(".error").remove();
        email.css("border", "none");

        if (!email.val()) {
            $(".error").remove();
            email.css("border", "1px solid red");
            email.after("<p class = 'error'>Please enter an email address!</p>");
            return;
        }

        //POST to request email recovery
        var inputData = {
            email: email.val(),
            recover: 1
        };

        $("#progress").show();

        $.ajax({
           url: "/api/users.php",
           type: "POST",
           data: JSON.stringify(inputData),
           success: function(data, xhr, status){


                $("#account").text("Recovery email sent if account exists.\nPlease check your email for further instructions.");
                //$("#account").after("<p class = 'error'>Redirecting to login page in 10 seconds...</p>");
                $("#progress").hide();
                $("#recoveryForm").hide();

                // setTimeout(function(){
                // //do what you need here
                //     window.location.replace("https://secure28.webhostinghub.com/~pocket14/forsyth.im/caterpillars/web/login.html");
                // }, 10000);


           },
           error: function(xhr,status){

                $("#account").text("Recovery email sent if account exists.\nPlease check your email for further instructions.");
                //$("#account").after("<p class = 'error'>Redirecting to login page in 10 seconds...</p>");
                $("#progress").hide();
                $("#recoveryForm").hide();



           }
        });


        $("#progress").hide();



    });




});
