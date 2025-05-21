function validateForm() {
  const dni = document.getElementById("usuario").value.trim();
  const password = document.getElementById("password").value.trim();
  const errorMsg = document.getElementById("error");

  if (dni === "" || password === "") {
    errorMsg.textContent = "Por favor completa todos los campos.";
    return false;
  }
  return true;
}
