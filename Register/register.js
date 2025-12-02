
function register() {
    let name = document.getElementById("fullname").value;
    let email = document.getElementById("email").value;
    let birthdate = document.getElementById("birthdate").value;
    let pass = document.getElementById("password").value;
    let confirm = document.getElementById("confirm_password").value;

    if (!name || !email || !birthdate || !pass || !confirm) {
        alert("Please fill in all fields, including birthdate.");
        return;
    }

    if (pass !== confirm) {
        alert("Passwords do not match!");
        return;
    }

    alert("Account created successfully!");
    window.location.href = "login.html";
}
