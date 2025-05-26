function validateLoginForm() {
  const usuario = document.getElementById('usuario').value.trim();
  const password = document.getElementById('password').value.trim();
  const errorElement = document.querySelector('.error-message');

  errorElement.textContent = '';

  if (!usuario || !password) {
    errorElement.textContent = 'Por favor, completa todos los campos.';
    return false;
  }

  return true;
}

function validateRegisterForm() {
  const nombre = document.getElementById('nombre').value.trim();
  const apellido1 = document.getElementById('apellido1').value.trim();
  const dni = document.getElementById('dni').value.trim();
  const email = document.getElementById('email').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
  const genero = document.getElementById('genero').value;
  const password = document.getElementById('password').value.trim();
  const errorElement = document.querySelector('.error-message');

  errorElement.textContent = '';

  // Validar nombre y apellido
  if (!nombre || !apellido1) {
    errorElement.textContent = 'El nombre y el primer apellido son obligatorios.';
    return false;
  }

  // Validar DNI (formato simple: 8 dígitos + 1 letra)
  const dniRegex = /^[0-9]{8}[A-Za-z]$/;
  if (!dniRegex.test(dni)) {
    errorElement.textContent = 'El DNI debe tener 8 dígitos seguidos de una letra (ej. 12345678A).';
    return false;
  }

  // Validar email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    errorElement.textContent = 'Por favor, introduce un correo electrónico válido.';
    return false;
  }

  // Validar teléfono (opcional, pero si se introduce debe ser válido)
  if (telefono && !/^[6789]\d{8}$/.test(telefono)) {
    errorElement.textContent = 'El teléfono debe comenzar con 6, 7, 8 o 9 y tener 9 dígitos.';
    return false;
  }

  // Validar fecha de nacimiento
  if (!fechaNacimiento) {
    errorElement.textContent = 'La fecha de nacimiento es obligatoria.';
    return false;
  }

  // Validar género
  if (!genero) {
    errorElement.textContent = 'Por favor, selecciona un género.';
    return false;
  }

  // Validar contraseña
  if (password.length < 6) {
    errorElement.textContent = 'La contraseña debe tener al menos 6 caracteres.';
    return false;
  }

  return true;
}