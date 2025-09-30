// Inicializar el mapa
const map = L.map('mapa-ubicacion').setView([-17.7833, -63.1833], 13);

// Añadir capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Añadir control de búsqueda
const geocoder = L.Control.Geocoder.nominatim();
L.Control.geocoder({
        defaultMarkGeocode: false,
        geocoder: geocoder
    })
    .on('markgeocode', function(e) {
        const latlng = e.geocode.center;
        updateLocation(latlng.lat, latlng.lng, e.geocode.name);
    })
    .addTo(map);

// Marcador para la ubicación seleccionada
let marker = null;

// Función para actualizar la ubicación
function updateLocation(lat, lng, address = null) {
    // Actualizar los campos de coordenadas
    document.getElementById('latitud').value = lat;
    document.getElementById('longitud').value = lng;

    // Actualizar la dirección si se proporciona
    if (address) {
        document.getElementById('direccion').value = address;
    } else {
        // Hacer geocodificación inversa para obtener la dirección
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById('direccion').value = data.display_name;
                }
            })
            .catch(error => console.error('Error al obtener la dirección:', error));
    }

    // Eliminar el marcador anterior si existe
    if (marker) {
        map.removeLayer(marker);
    }

    // Añadir nuevo marcador
    marker = L.marker([lat, lng]).addTo(map)
        .bindPopup('Tu ubicación seleccionada')
        .openPopup();

    // Centrar el mapa en la nueva ubicación
    map.setView([lat, lng], 15);
}

// Manejar el clic en el mapa
map.on('click', function(e) {
    updateLocation(e.latlng.lat, e.latlng.lng);
});

// Función para mostrar/ocultar contraseña
function togglePasswordVisibility(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = passwordInput.nextElementSibling;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '👁️‍🗨️';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}

// Manejar la subida de foto
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Mostrar la imagen previa
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-img').style.display = 'block';
            document.getElementById('default-icon').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Función para manejar la cámara
let stream = null;

function openCamera() {
    const modal = document.getElementById('camera-modal');
    modal.style.display = 'flex';

    navigator.mediaDevices.getUserMedia({
            video: true
        })
        .then(function(s) {
            stream = s;
            document.getElementById('camera-view').srcObject = stream;
        })
        .catch(function(err) {
            console.error("Error al acceder a la cámara: ", err);
            alert("No se pudo acceder a la cámara. Asegúrate de haber dado los permisos necesarios.");
            closeCamera();
        });
}

function closeCamera() {
    const modal = document.getElementById('camera-modal');
    modal.style.display = 'none';

    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

function takePhoto() {
    const video = document.getElementById('camera-view');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    const photoData = canvas.toDataURL('image/png');
    document.getElementById('preview-img').src = photoData;
    document.getElementById('preview-img').style.display = 'block';
    document.getElementById('default-icon').style.display = 'none';

    // Convertir la imagen a un Blob y asignarla al campo de foto_perfil
    const byteString = atob(photoData.split(',')[1]);
    const arrayBuffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(arrayBuffer);

    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }

    const file = new Blob([uintArray], {
        type: 'image/png'
    });

    // Crear un nuevo objeto File para que sea compatible con el formulario
    const fileInput = document.getElementById('foto');
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(new File([file], 'photo.png', {
        type: 'image/png'
    }));

    // Asignar el archivo generado al campo de entrada de archivo
    fileInput.files = dataTransfer.files;

    closeCamera();
}

// Tour con driver.js
function iniciarTourRegistro() {
    const driver = new Driver({
        className: "scoped-class",
        animate: true,
        opacity: 0.75,
        padding: 10,
        allowClose: true,
        overlayClickNext: false,
        doneBtnText: "Finalizar",
        closeBtnText: "Cerrar",
        nextBtnText: "Siguiente",
        prevBtnText: "Anterior"
    });

    // Definir los pasos del tour
    driver.defineSteps([
        {
            element: "#titulo-registro",
            popover: {
                title: "🏠 Página de Registro",
                description: "Bienvenido al sistema de registro. Te guiaremos paso a paso para crear tu cuenta.",
                position: "bottom"
            }
        },
        {
            element: "#foto",
            popover: {
                title: "📷 Foto de Perfil",
                description: "Puedes subir una foto desde tu dispositivo o tomar una con la cámara.",
                position: "bottom"
            }
        },
        {
            element: "#nombre",
            popover: {
                title: "👤 Nombre Completo",
                description: "Ingresa tu nombre completo tal como aparece en tu identificación oficial.",
                position: "right"
            }
        },
        {
            element: "#telefono",
            popover: {
                title: "📞 Teléfono",
                description: "Ingresa tu número de teléfono de 8 dígitos sin prefijo.",
                position: "right"
            }
        },
        {
            element: "#email",
            popover: {
                title: "📧 Correo Electrónico",
                description: "Proporciona un correo electrónico válido para verificación y notificaciones.",
                position: "right"
            }
        },
        {
            element: "#password",
            popover: {
                title: "🔒 Contraseña",
                description: "Crea una contraseña segura con letras, números y símbolos.",
                position: "right"
            }
        },
        {
            element: "#genero-container",
            popover: {
                title: "⚤ Género",
                description: "Selecciona tu género.",
                position: "right"
            }
        },
        {
            element: "#confirm-password",
            popover: {
                title: "✅ Confirmar Contraseña",
                description: "Vuelve a escribir tu contraseña para verificar que coincida.",
                position: "right"
            }
        },
        {
            element: "#direccion",
            popover: {
                title: "🏠 Dirección",
                description: "Ingresa tu dirección completa o selecciona en el mapa.",
                position: "right"
            }
        },
        {
            element: "#mapa-ubicacion",
            popover: {
                title: "🗺️ Ubicación en Mapa",
                description: "Selecciona tu ubicación exacta haciendo clic en el mapa.",
                position: "left"
            }
        },
        {
            element: "#btn-registrar",
            popover: {
                title: "🚀 Finalizar Registro",
                description: "Haz clic aquí para completar tu registro después de llenar todos los campos.",
                position: "top"
            }
        }
    ]);

    // Iniciar el tour
    driver.start();
}

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const latitud = document.getElementById('latitud').value;
            const longitud = document.getElementById('longitud').value;
            const telefono = document.getElementById('telefono').value;
            const direccion = document.getElementById('direccion').value;

            // Validación del lado del cliente
            let isValid = true;

            // Validar que las contraseñas coincidan
            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                isValid = false;
            }

            // Validar que la ubicación haya sido seleccionada
            if (!latitud || !longitud) {
                alert('Por favor selecciona tu ubicación en el mapa');
                isValid = false;
            }

            // Validar el número de teléfono boliviano (8 dígitos)
            if (!/^[0-9]{8}$/.test(telefono)) {
                alert('Por favor ingresa un número de teléfono válido de 8 dígitos');
                isValid = false;
            }

            // Validar que la dirección no esté vacía
            if (!direccion.trim()) {
                alert('Por favor ingresa tu dirección');
                isValid = false;
            }

            // Si todo está bien, enviar el formulario
            if (isValid) {
                this.submit();
            }
        });

        // Crear botón de tour
        const tourBtn = document.createElement('button');
        tourBtn.innerHTML = '💡';
        tourBtn.className = 'tour-btn';
        tourBtn.title = 'Iniciar Tour Guiado';
        tourBtn.onclick = iniciarTourRegistro;
        document.body.appendChild(tourBtn);
    }
});