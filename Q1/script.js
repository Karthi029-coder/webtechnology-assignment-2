const form = document.getElementById("registrationForm");
const $ = (id) => document.getElementById(id);

// Live password strength meter
$("password").addEventListener("input", function () {
  const p = this.value;
  let score = 0;
  if (p.length >= 8) score++;
  if (/[a-z]/.test(p) && /[A-Z]/.test(p)) score++;
  if (/\d/.test(p)) score++;
  if (/[^A-Za-z0-9]/.test(p)) score++;

  const levels = ["", "Weak", "Fair", "Good", "Strong"];
  const colors = ["transparent", "#ff6b81", "#ffb84d", "#4dc3ff", "#2ee6a6"];
  $("strengthBar").style.width = score * 25 + "%";
  $("strengthBar").style.background = colors[score];
  $("strengthText").textContent = p ? levels[score] : "";
});

function setError(id, message) {
  $(id + "Error").textContent = message;
  $(id).classList.add("invalid");
}

form.addEventListener("submit", function (event) {
  event.preventDefault();

  document.querySelectorAll(".error").forEach((e) => (e.textContent = ""));
  document.querySelectorAll(".invalid").forEach((e) => e.classList.remove("invalid"));
  $("success").textContent = "";

  const name = $("name").value.trim();
  const roll = $("roll").value.trim();
  const email = $("email").value.trim();
  const course = $("course").value;
  const password = $("password").value;
  const file = $("document").files[0];
  let valid = true;

  if (name === "") { setError("name", "Name is required"); valid = false; }
  if (roll === "") { setError("roll", "Roll number is required"); valid = false; }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    setError("email", "Enter a valid email address"); valid = false;
  }
  if (course === "") { setError("course", "Please select a course"); valid = false; }

  if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(password)) {
    setError("password", "Use 8+ characters with uppercase, lowercase, number and symbol");
    valid = false;
  }

  if (!file) {
    setError("document", "Please upload a document"); valid = false;
  } else if (!["application/pdf", "image/jpeg", "image/png"].includes(file.type)) {
    setError("document", "Upload a PDF, JPG or PNG file"); valid = false;
  } else if (file.size > 2 * 1024 * 1024) {
    setError("document", "File size must be below 2 MB"); valid = false;
  }

  if (!valid) return;

  const students = JSON.parse(localStorage.getItem("students") || "[]");
  if (students.some((s) => s.roll.toLowerCase() === roll.toLowerCase())) {
    setError("roll", "This roll number is already registered");
    return;
  }

  students.push({ name, roll, email, course, document: file.name });
  localStorage.setItem("students", JSON.stringify(students));

  $("success").textContent = "Registration successful!";
  $("strengthBar").style.width = "0";
  $("strengthText").textContent = "";
  form.reset();
});