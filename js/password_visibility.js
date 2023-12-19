window.onload = function () {
	const togglePassword = document.getElementById("togglePassword");
	const password = document.getElementById('password');

	togglePassword.onclick = function() {
	    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
	    password.setAttribute('type', type);
	    this.classList.toggle('fa-eye-slash');
	};
}