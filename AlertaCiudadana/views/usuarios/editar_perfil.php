<?php
ob_start();
require_once __DIR__ . "../../../controllers/usuario_controller.php";
require_once __DIR__ . '../../../templates/header.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioController = new UsuarioController();
$mensaje = '';
$error = '';

// Precargar datos del usuario
try {
    if (isset($_SESSION['usuario_id'])) {
        $datosUsuario = $usuarioController->obtenerDatosUsuario($_SESSION['usuario_id']);

        // Guardar datos en sesión para el formulario
        $_SESSION['usuario_nombre'] = $datosUsuario['nombre'];
        $_SESSION['usuario_email'] = $datosUsuario['email'];
        $_SESSION['usuario_genero'] = $datosUsuario['genero'];
        $_SESSION['usuario_direccion'] = $datosUsuario['direccion'];
        $_SESSION['usuario_telefono'] = $datosUsuario['telefono'];
        $_SESSION['usuario_latitud'] = $datosUsuario['latitud'];
        $_SESSION['usuario_longitud'] = $datosUsuario['longitud'];

        // Guardar foto en base64 para mostrarla
        if ($datosUsuario['foto_perfil']) {
            $_SESSION['usuario_foto_base64'] = 'data:image/jpeg;base64,' . $datosUsuario['foto_perfil'];
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Procesar actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    try {
        $datosActualizados = [
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'genero' => $_POST['genero'] ?? 'otro',
            'direccion' => $_POST['direccion'],
            'telefono' => $_POST['telefono'],
            'latitud' => $_POST['latitud'],
            'longitud' => $_POST['longitud']
        ];

        // Manejar foto si se subió una nueva
        if (!empty($_POST['foto_perfil_base64'])) {
            $datosActualizados['foto_perfil_base64'] = $_POST['foto_perfil_base64'];
        } elseif (!empty($_FILES['foto_perfil']['tmp_name'])) {
            $datosActualizados['foto_perfil_base64'] = base64_encode(file_get_contents($_FILES['foto_perfil']['tmp_name']));
        }

        // Manejar contraseña si se cambió
        if (!empty($_POST['password'])) {
            $datosActualizados['password'] = $_POST['password'];
        }

        $resultado = $usuarioController->actualizarUsuario($_SESSION['usuario_id'], $datosActualizados);

        if ($resultado) {
            $mensaje = "Perfil actualizado correctamente";

            // Actualizar datos en sesión
            $_SESSION['usuario_nombre'] = $datosActualizados['nombre'];
            $_SESSION['usuario_email'] = $datosActualizados['email'];

            if (isset($datosActualizados['foto_perfil_base64'])) {
                $_SESSION['usuario_foto_base64'] = 'data:image/jpeg;base64,' . $datosActualizados['foto_perfil_base64'];
            }

            // Recargar la página para ver los cambios
            header("Location: router.php?page=editar_perfil");
            exit();
        } else {
            $error = "Error al actualizar el perfil";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<style>
    /* Estilos específicos para la página de registro */
    .signup-section {
        max-width: 1200px;
        /* Aumenté de 1000px a 1200px */
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .signup-container {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        /* Cambié de 1fr 1fr a 1.2fr 1fr */
        gap: 2rem;
    }

    .signup-card {
        background: white;
        border-radius: 8px;
        padding: 2.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
        /* Asegura que ocupe todo el espacio disponible */
    }

    .map-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px15px rgba(0, 0, 0, 0.1);
        height: 100%;
        overflow: hidden;
        position: relative;
        z-index: 10;
    }

    #map {
        width: 100%;
        height: 100%;
        min-height: 500px;
    }

    .signup-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .signup-header h2 {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .signup-header p {
        color: #666;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--dark-color);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border 0.3s;
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        outline: none;
    }

    .coordinates-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* Nuevos estilos para el campo de género */
    .gender-options {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .gender-option {
        display: flex;
        align-items: center;
    }

    .gender-option input[type="radio"] {
        margin-right: 0.5rem;
    }

    .photo-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 2rem;
    }

    .photo-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #f5f5f5;
        margin-bottom: 1rem;
        overflow: hidden;
        position: relative;
        border: 3px solid var(--light-color);
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-options {
        display: flex;
        gap: 1rem;
    }

    .photo-btn {
        padding: 0.5rem 1rem;
        background: var(--light-color);
        color: var(--dark-color);
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background 0.3s;
    }

    .photo-btn:hover {
        background: #ddd;
    }

    .photo-btn.primary {
        background: var(--secondary-color);
        color: white;
    }

    .photo-btn.primary:hover {
        background: var(--primary-color);
    }

    #camera-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .camera-container {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
    }

    #camera-view {
        width: 100%;
        background: #333;
        margin-bottom: 1rem;
    }

    .camera-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .signup-btn {
        width: 100%;
        padding: 0.8rem;
        background: var(--secondary-color);
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .signup-btn:hover {
        background: var(--primary-color);
    }

    .login-footer {
        text-align: center;
        margin-top: 1.5rem;
        color: #666;
    }

    .login-footer a {
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    .password-container {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
    }

    @media (max-width: 768px) {
        .signup-container {
            grid-template-columns: 1fr;
        }

        .signup-card {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .photo-options {
            flex-direction: column;
            width: 100%;
        }

        .photo-btn {
            width: 100%;
        }

        .gender-options {
            flex-direction: column;
            gap: 0.5rem;
        }

        #map {
            min-height: 300px;
        }
    }

    .error-message {
        color: #dc3545;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .success-message {
        color: #28a745;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>

<div class="signup-section">
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <div class="success-message"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h2>Modificar Perfil</h2>
                <p>Mantén siempre actualizados tus datos</p>
            </div>

            <form id="signup-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="foto_perfil_base64" name="foto_perfil_base64">

                <div class="photo-upload">
                    <div class="photo-preview" id="photo-preview">
                        <?php if (!empty($_SESSION['usuario_foto_base64'])): ?>
                            <img id="preview-img" src="<?php echo $_SESSION['usuario_foto_base64']; ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <div id="default-icon" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; color: #999;">
                                <i class="fas fa-user" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="photo-input" name="foto_perfil" accept="image/*" style="display: none;" onchange="previewImage(event)">
                    <div class="photo-options">
                        <button type="button" class="photo-btn" onclick="document.getElementById('photo-input').click()">Subir foto</button>
                        <button type="button" class="photo-btn primary" onclick="openCamera()">Tomar foto</button>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" class="form-control"
                            placeholder="Ingresa tu nombre completo" required
                            value="<?php echo isset($_SESSION['usuario_nombre']) ? htmlspecialchars($_SESSION['usuario_nombre']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" class="form-control"
                            placeholder="Ej: 70012345" pattern="[0-9]{8}" required
                            value="<?php echo isset($_SESSION['usuario_telefono']) ? htmlspecialchars($_SESSION['usuario_telefono']) : ''; ?>">
                        <small style="font-size: 0.8rem; color: #666;">Ingresa tu número de 8 dígitos sin prefijo</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="tucorreo@ejemplo.com" required
                            value="<?php echo isset($_SESSION['usuario_email']) ? htmlspecialchars($_SESSION['usuario_email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Crea una contraseña segura">
                            <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Género</label>
                        <div class="gender-options">
                            <div class="gender-option">
                                <input type="radio" id="genero-masculino" name="genero" value="masculino" required
                                    <?php echo ($_SESSION['usuario_genero'] == 'masculino') ? 'checked' : ''; ?>>
                                <label for="genero-masculino">Masculino</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" id="genero-femenino" name="genero" value="femenino"
                                    <?php echo ($_SESSION['usuario_genero'] == 'femenino') ? 'checked' : ''; ?>>
                                <label for="genero-femenino">Femenino</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" id="genero-otro" name="genero" value="otro"
                                    <?php echo ($_SESSION['usuario_genero'] == 'otro') ? 'checked' : ''; ?>>
                                <label for="genero-otro">Otro</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirmar contraseña</label>
                        <div class="password-container">
                            <input type="password" id="confirm-password" name="confirm-password" class="form-control"
                                placeholder="Repite tu contraseña">
                            <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">👁️</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección de tu casa</label>
                    <input type="text" id="direccion" name="direccion" class="form-control"
                        placeholder="Ej: Av. San Martín #123" required
                        value="<?php echo isset($_SESSION['usuario_direccion']) ? htmlspecialchars($_SESSION['usuario_direccion']) : ''; ?>">
                </div>

                <div class="form-group">
                    <div class="coordinates-container">
                        <div class="form-group">
                            <label for="latitud">Latitud</label>
                            <input type="text" id="latitud" name="latitud" class="form-control"
                                placeholder="Ej: -17.7833" required readonly
                                value="<?php echo isset($_SESSION['usuario_latitud']) ? htmlspecialchars($_SESSION['usuario_latitud']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="longitud">Longitud</label>
                            <input type="text" id="longitud" name="longitud" class="form-control"
                                placeholder="Ej: -63.1833" required readonly
                                value="<?php echo isset($_SESSION['usuario_longitud']) ? htmlspecialchars($_SESSION['usuario_longitud']) : ''; ?>">
                        </div>
                    </div>
                    <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem;">Selecciona tu ubicación en el mapa al lado</p>
                </div>

                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
        </div>

        <div class="map-card">
            <div id="map"></div>
        </div>
    </div>
</div>

<!-- Modal para tomar foto con cámara -->
<div id="camera-modal">
    <div class="camera-container">
        <video id="camera-view" autoplay playsinline></video>
        <div class="camera-buttons">
            <button type="button" class="photo-btn" onclick="closeCamera()">Cancelar</button>
            <button type="button" class="photo-btn primary" onclick="takePhoto()">Tomar foto</button>
        </div>
    </div>
</div>

<!-- Incluir Leaflet CSS y JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- Incluir Nominatim para geocodificación inversa -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    // Inicializar el mapa
    const map = L.map('map').setView([-17.7833, -63.1833], 13); // Coordenadas de Santa Cruz de la Sierra

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

    // Verificar si el usuario tiene latitud y longitud guardadas
    window.onload = function() {
        const latitud = "<?php echo isset($_SESSION['usuario_latitud']) ? $_SESSION['usuario_latitud'] : ''; ?>";
        const longitud = "<?php echo isset($_SESSION['usuario_longitud']) ? $_SESSION['usuario_longitud'] : ''; ?>";

        if (latitud && longitud) {
            // Si tiene coordenadas, cargar el marcador
            updateLocation(latitud, longitud);
        }
    };

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
                video: {
                    facingMode: 'user'
                } // Usar cámara frontal
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

        const photoData = canvas.toDataURL('image/jpeg');

        // Mostrar vista previa
        document.getElementById('preview-img').src = photoData;
        document.getElementById('preview-img').style.display = 'block';
        if (document.getElementById('default-icon')) {
            document.getElementById('default-icon').style.display = 'none';
        }

        // Guardar datos de la imagen para enviar (solo la parte base64)
        document.getElementById('foto_perfil_base64').value = photoData.split(',')[1];

        closeCamera();
    }

    // Función para vista previa de imagen subida
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-img').style.display = 'block';
                if (document.getElementById('default-icon')) {
                    document.getElementById('default-icon').style.display = 'none';
                }

                // Convertir a base64 para enviar
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);

                    // Calidad del 80% para reducir tamaño
                    const base64 = canvas.toDataURL('image/jpeg', 0.8).split(',')[1];
                    document.getElementById('foto_perfil_base64').value = base64;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Validación del formulario
    document.getElementById('signup-form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }

        return true;
    });
</script>

<?php
require_once __DIR__ . "../../../templates/footer.php";
ob_end_flush();
?>