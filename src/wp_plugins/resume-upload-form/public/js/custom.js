$().ready(function() {

	var processUrl = script_name.processUrl;
	var captchaUrl = script_name.captchaUrl;
	
	// Form validation on keyup and submit
	$("#resume_form").validate({

	    rules: {
	        fname: {required: true, lettersonly: true},
	        lname: {required: true, lettersonly: true},                               
	        email: {required: true, email: true},
	        phone: {phoneINT: true},
	        exp: "required",
	        file: {required: true, extension: "doc|docx|pdf"},
	        captcha: {
				required: true,
				remote: processUrl
			}
	    },
	    messages: {                
	        fname: {
	            required: "Please enter your firstname",
	            lettersonly: "Please enter letters only"
	        },
	        lname: {
	            required: "Please enter your lastname",
	            lettersonly: "Please enter letters only"
	        },                
	        email: "Please enter a valid email",
	        phone: {
	            phoneINT: "Please specify a valid mobile number"
	        },
	        exp: "Please select experience",
	        file: {
	            required: "Please select resume",
	            extension: "Only select doc,docx,pdf file"
	        },
	        captcha: {
				required: "Correct captcha is required",
				remote: "Enter correct captcha number"
			}
	    }
	});

	$("body").on("click", "#refreshimg", function() {
		document.getElementById('captchaImg').src = captchaUrl;
  		document.getElementById('captcha').value = '';
		return false;
	});

});

if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}