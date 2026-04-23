const API_BASE = '/conduit/api';
document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("register-form");

  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const username = registerForm.querySelector('input[placeholder="Username"]').value;
      const email = registerForm.querySelector('input[placeholder="Email"]').value;
      const password = registerForm.querySelector('input[placeholder="Password"]').value;

      try {
        const res = await fetch(`${API_BASE}/users`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Token ${localStorage.getItem("token") || ""}`,
          },
          body: JSON.stringify({
            user: { username, email, password },
          }),
        });

        const data = await res.json();

        if (!res.ok) {
          showErrors(data.errors);
          return;
        }

        localStorage.setItem("token", data.user.token);

        window.location.href = "/conduit/";

      } catch (err) {
        console.error(err);
      }
    });
  }
});

// login form handling

document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("login-form");

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = loginForm.querySelector('input[placeholder="Email"]').value;
      const password = loginForm.querySelector('input[placeholder="Password"]').value;

      try {
        const res = await fetch(`${API_BASE}/users/login`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Token ${localStorage.getItem("token") || ""}`,
          },
          body: JSON.stringify({
            user: { email, password },
          }),
        });

        const data = await res.json();
        console.log(data)

        if (!res.ok) {
          showErrors(data.errors);
          return;
        }

        // ✅ Store JWT
        localStorage.setItem("token", data.user.token);

        // ✅ Redirect to home
        window.location.href = "/conduit/";

      } catch (err) {
        console.error(err);
      }
    });
  }
});