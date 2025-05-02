document.getElementById("signupForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const name = document.getElementById("signupName").value;
    const email = document.getElementById("signupEmail").value;
    const password = document.getElementById("signupPassword").value;
  
    const user = { name, email, password };
    localStorage.setItem("movievibeUser", JSON.stringify(user));
  
    alert("Inscription réussie !");
    window.location.href = "login.html";
  });
  