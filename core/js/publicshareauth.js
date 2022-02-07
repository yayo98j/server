function showEmailAddressPrompt() {
	var emailInput = document.getElementById('email-input');
	var emailPrompt = document.getElementById('email-prompt');
	emailInput.style.display="block";
	emailPrompt.style.display="block";

	var passwordRequestButton = document.getElementById('request-password-button');
	var passwordInput = document.getElementById('password-input');
	passwordRequestButton.style.display="none";
	passwordInput.style.display="none";
}

document.addEventListener('DOMContentLoaded', function() {
	var passwordInput = document.getElementById('password');
	var passwordButton = document.getElementById('password-submit');
	var eventListener = function() {
		passwordButton.disabled = passwordInput.value.length === 0;
	};

	passwordInput.addEventListener('click', eventListener);
	passwordInput.addEventListener('keyup', eventListener);
	passwordInput.addEventListener('change', eventListener);

	var passwordRequestButton = document.getElementById('request-password-button');
	passwordRequestButton.addEventListener('click', showEmailAddressPrompt);

});
