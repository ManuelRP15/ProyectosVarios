let img = null;
let canvas = document.createElement("canvas");
let ctx = canvas.getContext("2d");
let originalImage = document.getElementById("originalImage");
let enhancedImage = document.getElementById("enhancedImage");

document.getElementById("upload").addEventListener("change", function (event) {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    img = new Image();
    img.src = e.target.result;
    img.onload = function () {
      // Establecer la imagen original en su contenedor
      originalImage.src = img.src;
    };
  };
  reader.readAsDataURL(file);
});

document.getElementById("enhanceButton").addEventListener("click", function () {

  // Ajustar tamaño del canvas
  canvas.width = img.width;
  canvas.height = img.height;

  // Dibujar la imagen original en el canvas
  ctx.drawImage(img, 0, 0);

  const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  const data = imageData.data;

  // Aumentar el contraste y el brillo de la imagen
  for (let i = 0; i < data.length; i += 4) {
    data[i] = Math.min(255, data[i] * 1.5); // Rojo
    data[i + 1] = Math.min(255, data[i + 1] * 1.5); // Verde
    data[i + 2] = Math.min(255, data[i + 2] * 1.5); // Azul
  }

  ctx.putImageData(imageData, 0, 0);

  // Convertir el canvas a una imagen y previsualizarla
  enhancedImage.src = canvas.toDataURL();


  // Mostrar el botón de descarga
  const downloadButton = document.getElementById("download");
  downloadButton.style.display = "inline-block";

  // Crear un enlace de descarga
  downloadButton.addEventListener("click", function () {
    const link = document.createElement("a");
    link.href = canvas.toDataURL("image/png");
    link.download = "imagen_mejorada.png";
    link.click();
  });
});
