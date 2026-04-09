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