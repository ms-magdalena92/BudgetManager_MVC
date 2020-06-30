$.validator.addMethod('validName',
	function(value, element, param) {
		
		if (value != '') {
			
			if (value.match(/^[A-Za-z]+$/)) {
				return true;
			}
		}
		return false;
	},
	'Name must contain letters only, special characters not allowed.'
);
		
$.validator.addMethod('validPassword',
	function(value, element, param) {
		
		if (value != '') {
			
			if (value.match(/.*[a-z]+.*/i) == null) {
				return false;
			}
			
			if (value.match(/.*\d+.*/) == null) {
				return false;
			}
		}
		return true;
	},
	'Password must contain at least one letter and at least one number.'
);
		
$(document).ready(function() {
	$('#signupForm').validate({
		errorElement: 'li',
		rules: {
			userName: {
				required: true,
				minlength: 2,
				maxlength: 20,
				validName: true
			},
			email: {
				required: true,
				email: true,
				remote: '/account/validate-email'
			},
			password: {
				required: true,
				minlength: 8,
				maxlength: 50,
				validPassword: true
			},
			passwordConfirm: {
				required: true,
				equalTo: '#password1'
			}
		},
		messages: {
			userName: {
				required: 'Name is required.',
				minlength: 'Name needs to be between 2 to 20 characters.',
				maxlength: 'Name needs to be between 2 to 20 characters.'
			},
			email: {
				required: 'E-mail adress is required.',
				email: 'Please enter a valid e-mail adress.',
				remote: 'An account with this e-mail adress already exists.'
			},
			password: {
				required: 'Password is required.',
				minlength: 'Password needs to be between 8 to 50 characters.',
				maxlength: 'Password needs to be between 8 to 50 characters.'
			},
			passwordConfirm: {
				required: 'Password confirmation is required.',
				equalTo: 'Passwords you have entered does not match.'
			}
		},
		errorPlacement: function(error,element){
			
			if(element.attr('name') == 'userName') {
				error.appendTo('#nameError');
			}
			if(element.attr('name') == 'email') {
				error.appendTo('#emailError');
			}
			if(element.attr('name') == 'password') {
				error.appendTo('#passwordError');
			}
			if(element.attr('name') == 'passwordConfirm') {
				error.appendTo('#passwordConfirmError');
			}
		}	
	});
});
		
$(".reveal").on('click',function() {
	
	var $password = $("#password1");
	
	if ($password.attr('type') === 'password') {
		
		$password.attr('type', 'text');
		$("#toggler").removeClass("icon-eye");
		$("#toggler").addClass("icon-eye-off");
		
	} else {
		
		$password.attr('type', 'password');
		$("#toggler").removeClass("icon-eye-off");
		$("#toggler").addClass("icon-eye");
	}
});