// Función para validar que la entrada contenga solo letras mayúsculas y el carácter especial espacio (para el primer caso)
function validarMayusculas(input) {
  var regex = /^[A-ZÑ\s]*$/; // Expresión regular para letras mayúsculas y espacio
  if (!regex.test(input.value)) {
      input.value = input.value.slice(0, -1); // Elimina el último carácter si no cumple con el patrón
  }
}

// Función para validar que la entrada contenga letras mayúsculas, números y ciertos caracteres especiales (para el segundo caso)
function validarMayusculasNUMEROS(input) {
  var regex = /^[A-ZÑ0-9\s!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~]*$/; // Expresión regular para letras mayúsculas, números y caracteres especiales
  if (!regex.test(input.value)) {
      input.value = input.value.slice(0, -1); // Elimina el último carácter si no cumple con el patrón
  }
}

// Función para permitir solo letras y el espacio (sin distinción entre mayúsculas y minúsculas)
function sololetras(e) {
  key = e.keyCode || e.which;
  teclado = String.fromCharCode(key).toLowerCase();
  letras = " abcdefghijklmnñopqrstuvwxyz" // Lista de letras y espacio
  especiales = "8-37-38-46-164" // Códigos de teclas especiales
  teclado_especial = false;
  for (var i in especiales) {
      if (key == especiales[i]) {
          teclado_especial = true;
          break;
      }
  }
  if (letras.indexOf(teclado) == -1 && !teclado_especial) {
      return false; // Evita que se ingrese el carácter si no es una letra o un carácter especial permitido
  }
}

// Función para permitir solo números y ciertas teclas especiales
function solonumeros(e) {
  key = e.keyCode || e.which;
  teclado2 = String.fromCharCode(key).toLowerCase();
  numero = "0123456789"; // Lista de números
  especiales = "8-37-38-46"; // Códigos de teclas especiales
  teclado_especiall = false;
  for (var i in especiales) {
      if (key == especiales[i]) {
          teclado_especiall = true;
          break;
      }
  }
  if (numero.indexOf(teclado2) == -1 && !teclado_especiall) {
      return false; // Evita que se ingrese el carácter si no es un número o una tecla especial permitida
  }

  // Validación para permitir solo 10 dígitos
  input = e.target;
  if (input.value.length >= 10) {
      return false; // Evita que se ingresen más de 10 dígitos
  }
}

// Evento que se dispara cuando se ha cargado completamente el contenido de la página
document.addEventListener('DOMContentLoaded', function() {
// Selecciona todos los formularios en la página
var forms = document.querySelectorAll('form');

// Agrega un controlador de eventos a cada formulario
forms.forEach(function(form) {
  form.addEventListener('keydown', function(event) {
    // Si la tecla presionada fue "Enter" y el elemento enfocado no es un área de texto
    if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
      // Previene la acción predeterminada (enviar el formulario)
      event.preventDefault();
    }
  });
});
});
