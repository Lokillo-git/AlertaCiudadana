<?php
// Incluir el controlador para registrar el usuario
require_once __DIR__ . "../../../controllers/usuario_controller.php";

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validaciones del servidor
    $errors = [];
    
    // Validar nombre (solo letras y espacios, mínimo 5 caracteres)
    if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
        $errors['nombre'] = 'El nombre completo es requerido';
    } else {
        $nombre = trim($_POST['nombre']);
        if (strlen($nombre) < 5) {
            $errors['nombre'] = 'El nombre debe tener al menos 5 caracteres';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
            $errors['nombre'] = 'El nombre solo puede contener letras y espacios';
        }
    }
    
    // Validar teléfono (solo números, exactamente 8 dígitos)
    if (!isset($_POST['telefono']) || empty(trim($_POST['telefono']))) {
        $errors['telefono'] = 'El teléfono es requerido';
    } else {
        $telefono = trim($_POST['telefono']);
        if (!preg_match('/^[0-9]{8}$/', $telefono)) {
            $errors['telefono'] = 'El teléfono debe tener exactamente 8 dígitos';
        }
    }
    
    // Validar email (formato válido de correo electrónico)
    if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
        $errors['email'] = 'El correo electrónico es requerido';
    } else {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El formato del correo electrónico no es válido';
        }
    }
    
    // Validar contraseña (mínimo 8 caracteres)
    if (!isset($_POST['password']) || empty($_POST['password'])) {
        $errors['password'] = 'La contraseña es requerida';
    } else {
        $password = $_POST['password'];
        if (strlen($password) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
        }
    }
    
    // Validar confirmación de contraseña
    if (!isset($_POST['confirm-password']) || empty($_POST['confirm-password'])) {
        $errors['confirm-password'] = 'La confirmación de contraseña es requerida';
    } elseif ($_POST['password'] !== $_POST['confirm-password']) {
        $errors['confirm-password'] = 'Las contraseñas no coinciden';
    }
    
    // Validar dirección (mínimo 8 caracteres)
    if (!isset($_POST['direccion']) || empty(trim($_POST['direccion']))) {
        $errors['direccion'] = 'La dirección es requerida';
    } else {
        $direccion = trim($_POST['direccion']);
        if (strlen($direccion) < 8) {
            $errors['direccion'] = 'La dirección debe tener al menos 8 caracteres';
        }
    }
    
    // Validar género
    if (!isset($_POST['genero']) || empty($_POST['genero'])) {
        $errors['genero'] = 'El género es requerido';
    }
    
    // Validar coordenadas
    if (!isset($_POST['latitud']) || empty(trim($_POST['latitud']))) {
        $errors['latitud'] = 'La latitud es requerida';
    }
    if (!isset($_POST['longitud']) || empty(trim($_POST['longitud']))) {
        $errors['longitud'] = 'La longitud es requerida';
    }
    
    // Si no hay errores, proceder con el registro
    if (empty($errors)) {
        // Crear una instancia del controlador
        $usuarioController = new UsuarioController();
        // Llamar al método registrar del controlador
        $usuarioController->registrar();
    } else {
        // Guardar los valores para mostrarlos en el formulario
        $nombre = isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '';
        $telefono = isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '';
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $direccion = isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : '';
        $latitud = isset($_POST['latitud']) ? htmlspecialchars($_POST['latitud']) : '';
        $longitud = isset($_POST['longitud']) ? htmlspecialchars($_POST['longitud']) : '';
    }
}

require_once __DIR__ . "../../../templates/header.php";
?>

<style>
    /* Estilos específicos para la página de registro */
    .signup-section {
        max-width: 1200px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .signup-container {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2rem;
    }

    .signup-card {
        background: white;
        border-radius: 8px;
        padding: 2.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
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

    .form-control.error {
        border-color: #dc3545;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .error-message {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
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

    /* Estilos para las notificaciones toast */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        background: #dc3545;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 300px;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }

    .toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .toast.hide {
        transform: translateX(400px);
        opacity: 0;
    }

    .toast-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .toast-content {
        flex: 1;
    }

    .toast-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toast.success {
        background: #28a745;
    }

    .toast.warning {
        background: #ffc107;
        color: #212529;
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

        .toast-container {
            left: 10px;
            right: 10px;
            top: 10px;
        }

        .toast {
            min-width: auto;
            width: calc(100% - 20px);
        }
    }

    .alert-error {
        color: #dc3545;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
        display: <?php echo !empty($errors) ? 'block' : 'none'; ?>;
    }
</style>

<!-- Contenedor para notificaciones toast -->
<div class="toast-container" id="toast-container"></div>

<div class="signup-section">
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h2>Crear una cuenta</h2>
                <p>Únete a Alerta Ciudadana y ayuda a mejorar tu ciudad</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert-error">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="signup-form" method="POST" enctype="multipart/form-data">
                <div class="photo-upload">
                    <div class="photo-preview" id="photo-preview">
                        <img id="preview-img" src="" alt="Previsualización de foto" style="display: none;">
                        <div id="default-icon" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; color: #999;">
                            <i class="fas fa-user" style="font-size: 3rem;"></i>
                        </div>
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
                        <input type="text" id="nombre" name="nombre" class="form-control <?php echo isset($errors['nombre']) ? 'error' : ''; ?>"
                            placeholder="Ingresa tu nombre completo" required
                            value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>">
                        <?php if (isset($errors['nombre'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['nombre']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" class="form-control <?php echo isset($errors['telefono']) ? 'error' : ''; ?>"
                            placeholder="Ej: 70012345" pattern="[0-9]{8}" required
                            value="<?php echo isset($telefono) ? htmlspecialchars($telefono) : ''; ?>">
                        <small style="font-size: 0.8rem; color: #666;">Ingresa tu número de 8 dígitos sin prefijo</small>
                        <?php if (isset($errors['telefono'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['telefono']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control <?php echo isset($errors['email']) ? 'error' : ''; ?>"
                            placeholder="tucorreo@ejemplo.com" required
                            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'error' : ''; ?>"
                                placeholder="Crea una contraseña segura" required>
                            <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Género</label>
                        <div class="gender-options">
                            <div class="gender-option">
                                <input type="radio" id="genero-masculino" name="genero" value="masculino" required
                                    <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'masculino') ? 'checked' : ''; ?>>
                                <label for="genero-masculino">Masculino</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" id="genero-femenino" name="genero" value="femenino"
                                    <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'femenino') ? 'checked' : ''; ?>>
                                <label for="genero-femenino">Femenino</label>
                            </div>
                        </div>
                        <?php if (isset($errors['genero'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['genero']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirmar contraseña</label>
                        <div class="password-container">
                            <input type="password" id="confirm-password" name="confirm-password" class="form-control <?php echo isset($errors['confirm-password']) ? 'error' : ''; ?>"
                                placeholder="Repite tu contraseña" required>
                            <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">👁️</span>
                        </div>
                        <?php if (isset($errors['confirm-password'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['confirm-password']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección de tu casa</label>
                    <input type="text" id="direccion" name="direccion" class="form-control <?php echo isset($errors['direccion']) ? 'error' : ''; ?>"
                        placeholder="Ej: Av. San Martín #123" required
                        value="<?php echo isset($direccion) ? htmlspecialchars($direccion) : ''; ?>">
                    <?php if (isset($errors['direccion'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['direccion']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="coordinates-container">
                        <div class="form-group">
                            <label for="latitud">Latitud</label>
                            <input type="text" id="latitud" name="latitud" class="form-control <?php echo isset($errors['latitud']) ? 'error' : ''; ?>"
                                placeholder="Ej: -17.7833" required readonly
                                value="<?php echo isset($latitud) ? htmlspecialchars($latitud) : ''; ?>">
                            <?php if (isset($errors['latitud'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['latitud']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="longitud">Longitud</label>
                            <input type="text" id="longitud" name="longitud" class="form-control <?php echo isset($errors['longitud']) ? 'error' : ''; ?>"
                                placeholder="Ej: -63.1833" required readonly
                                value="<?php echo isset($longitud) ? htmlspecialchars($longitud) : ''; ?>">
                            <?php if (isset($errors['longitud'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['longitud']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem;">Selecciona tu ubicación en el mapa al lado</p>
                </div>

                <button type="submit" class="signup-btn">Registrarse</button>

                <div class="login-footer">
                    <p>¿Ya tienes una cuenta? <a href="router.php?page=login">Inicia sesión aquí</a></p>
                </div>
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
    // Función para mostrar notificaciones toast
    function showToast(message, type = 'error') {
        const toastContainer = document.getElementById('toast-container');
        const toastId = 'toast-' + Date.now();
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.id = toastId;
        
        const icon = type === 'success' ? '✅' : type === 'warning' ? '⚠️' : '❌';
        
        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <div class="toast-content">${message}</div>
            <button class="toast-close" onclick="closeToast('${toastId}')">×</button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Mostrar toast con animación
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            closeToast(toastId);
        }, 5000);
    }

    // Función para cerrar toast
    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }

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
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                showToast('Por favor selecciona un archivo de imagen válido', 'error');
                return;
            }

            // Validar tamaño de archivo (máximo 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('La imagen no debe pesar más de 5MB', 'error');
                return;
            }

            // Mostrar la imagen previa
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-img').style.display = 'block';
                document.getElementById('default-icon').style.display = 'none';
                showToast('Imagen cargada correctamente', 'success');
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
                showToast('No se pudo acceder a la cámara. Asegúrate de haber dado los permisos necesarios.', 'error');
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
        const fileInput = document.getElementById('photo-input');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(new File([file], 'photo.png', {
            type: 'image/png'
        }));

        // Asignar el archivo generado al campo de entrada de archivo
        fileInput.files = dataTransfer.files;

        showToast('Foto tomada correctamente', 'success');
        closeCamera();
    }

    // Validación del formulario en tiempo real
    document.getElementById('signup-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const nombre = document.getElementById('nombre').value;
        const telefono = document.getElementById('telefono').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const latitud = document.getElementById('latitud').value;
        const longitud = document.getElementById('longitud').value;
        const direccion = document.getElementById('direccion').value;
        const genero = document.querySelector('input[name="genero"]:checked');

        // Validación del lado del cliente
        let isValid = true;
        const errors = {};

        // Validar nombre (solo letras y espacios, mínimo 5 caracteres)
        if (nombre.trim().length < 5) {
            errors.nombre = 'El nombre debe tener al menos 5 caracteres';
            showToast('El nombre debe tener al menos 5 caracteres', 'error');
            isValid = false;
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
            errors.nombre = 'El nombre solo puede contener letras y espacios';
            showToast('El nombre solo puede contener letras y espacios', 'error');
            isValid = false;
        }

        // Validar teléfono (solo números, exactamente 8 dígitos)
        if (!/^[0-9]{8}$/.test(telefono)) {
            errors.telefono = 'El teléfono debe tener exactamente 8 dígitos';
            showToast('El teléfono debe tener exactamente 8 dígitos', 'error');
            isValid = false;
        }

        // Validar email (formato válido)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errors.email = 'El formato del correo electrónico no es válido';
            showToast('El formato del correo electrónico no es válido', 'error');
            isValid = false;
        }

        // Validar contraseña (mínimo 8 caracteres)
        if (password.length < 8) {
            errors.password = 'La contraseña debe tener al menos 8 caracteres';
            showToast('La contraseña debe tener al menos 8 caracteres', 'error');
            isValid = false;
        }

        // Validar confirmación de contraseña
        if (password !== confirmPassword) {
            errors.confirmPassword = 'Las contraseñas no coinciden';
            showToast('Las contraseñas no coinciden', 'error');
            isValid = false;
        }

        // Validar dirección (mínimo 8 caracteres)
        if (direccion.trim().length < 8) {
            errors.direccion = 'La dirección debe tener al menos 8 caracteres';
            showToast('La dirección debe tener al menos 8 caracteres', 'error');
            isValid = false;
        }

        // Validar género
        if (!genero) {
            errors.genero = 'El género es requerido';
            showToast('Por favor selecciona tu género', 'error');
            isValid = false;
        }

        // Validar coordenadas
        if (!latitud || !longitud) {
            errors.coordenadas = 'Por favor selecciona tu ubicación en el mapa';
            showToast('Por favor selecciona tu ubicación en el mapa', 'error');
            isValid = false;
        }

        // Mostrar errores en tiempo real
        Object.keys(errors).forEach(field => {
            const errorElement = document.querySelector(`#${field} + .error-message`);
            if (errorElement) {
                errorElement.textContent = errors[field];
                document.getElementById(field).classList.add('error');
            }
        });

        // Si todo está bien, enviar el formulario
        if (isValid) {
            showToast('Registro exitoso! Redirigiendo...', 'success');
            // Pequeño delay para que se vea el mensaje de éxito
            setTimeout(() => {
                this.submit();
            }, 1500);
        } else {
            // Mostrar mensaje general de error
            showToast('Por favor corrige los errores en el formulario', 'error');
        }
    });

    // Validación en tiempo real para cada campo
    document.querySelectorAll('#signup-form input').forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Remover error cuando el usuario empiece a escribir
            this.classList.remove('error');
            const errorElement = this.nextElementSibling;
            if (errorElement && errorElement.classList.contains('error-message')) {
                errorElement.textContent = '';
            }
        });

        // Validación mientras escribe para algunos campos
        if (input.name === 'telefono') {
            input.addEventListener('input', function() {
                // Solo permitir números
                this.value = this.value.replace(/[^0-9]/g, '');
                // Limitar a 8 caracteres
                if (this.value.length > 8) {
                    this.value = this.value.slice(0, 8);
                }
            });
        }

        if (input.name === 'nombre') {
            input.addEventListener('input', function() {
                // Solo permitir letras y espacios
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            });
        }
    });

    function validateField(field) {
        const value = field.value.trim();
        let error = '';

        switch (field.name) {
            case 'nombre':
                if (value.length < 5) {
                    error = 'El nombre debe tener al menos 5 caracteres';
                    showToast('El nombre debe tener al menos 5 caracteres', 'error');
                } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value)) {
                    error = 'El nombre solo puede contener letras y espacios';
                    showToast('El nombre solo puede contener letras y espacios', 'error');
                }
                break;
                
            case 'telefono':
                if (!/^[0-9]{8}$/.test(value)) {
                    error = 'El teléfono debe tener exactamente 8 dígitos';
                    showToast('El teléfono debe tener exactamente 8 dígitos', 'error');
                }
                break;
                
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    error = 'El formato del correo electrónico no es válido';
                    showToast('El formato del correo electrónico no es válido', 'error');
                }
                break;
                
            case 'password':
                if (value.length < 8) {
                    error = 'La contraseña debe tener al menos 8 caracteres';
                    showToast('La contraseña debe tener al menos 8 caracteres', 'error');
                }
                break;
                
            case 'confirm-password':
                const password = document.getElementById('password').value;
                if (value !== password) {
                    error = 'Las contraseñas no coinciden';
                    showToast('Las contraseñas no coinciden', 'error');
                }
                break;
                
            case 'direccion':
                if (value.length < 8) {
                    error = 'La dirección debe tener al menos 8 caracteres';
                    showToast('La dirección debe tener al menos 8 caracteres', 'error');
                }
                break;
        }

        // Mostrar u ocultar error
        let errorElement = field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            if (error) {
                field.classList.add('error');
                errorElement.textContent = error;
            } else {
                field.classList.remove('error');
                errorElement.textContent = '';
                // Mostrar mensaje de éxito para campos válidos
                if (value.length > 0) {
                    showToast(`Campo ${field.name} válido`, 'success');
                }
            }
        }
    }

    // Validar automáticamente campos al cargar la página si tienen valores
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#signup-form input').forEach(input => {
            if (input.value.trim() !== '') {
                validateField(input);
            }
        });
    });
</script>

<?php
require_once __DIR__ . "../../../templates/footer.php";
?>