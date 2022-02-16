function showEmailAddressPrompt() {
	// Shows email prompt
	var emailInput = document.getElementById('email-input');
	var emailPrompt = document.getElementById('email-prompt');
	emailInput.style.display="block";
	emailPrompt.style.display="block";

	// Hides password prompt
	var passwordRequestButton = document.getElementById('request-password-button-not-talk');
	var passwordInput = document.getElementById('password-input');
	passwordRequestButton.style.display="none";
	passwordInput.style.display="none";

	// Hides identification result messages, if any
	var identificationResultSuccess = document.getElementById('identification-success');
	var identificationResultFailure = document.getElementById('identification-failure');
	if (identificationResultSuccess) {
		identificationResultSuccess.style.display="none";
	}
	if (identificationResultFailure) {
		identificationResultFailure.style.display="none";
	}
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

	var passwordRequestButton = document.getElementById('request-password-button-not-talk');
	passwordRequestButton.addEventListener('click', showEmailAddressPrompt);

});
