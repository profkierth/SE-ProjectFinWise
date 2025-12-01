function register() {
    let name = document.getElementById("fullname").value;
    let email = document.getElementById("email").value;
    let pass = document.getElementById("password").value;
    let confirm = document.getElementById("confirm_password").value;

    if (!name || !email || !pass || !confirm) {
        alert("Please fill in all fields.");
        return;
    }

    if (pass !== confirm) {
        alert("Passwords do not match!");
        return;
    }

    alert("Account created successfully!");
    window.location.href = "login.html";
}

function login() {
    let email = document.getElementById("email").value;
    let pass = document.getElementById("password").value;

    if (!email || !pass) {
        alert("Please fill in all fields.");
        return;
    }

    alert("Login successful!");
    window.location.href = "dashboard.html";
}
