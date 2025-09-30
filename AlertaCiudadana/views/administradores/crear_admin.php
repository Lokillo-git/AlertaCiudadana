<?php
ob_start();
require_once __DIR__ . '../../../templates/layout_admin.php';
require_once __DIR__ . '../../../controllers/admin_controller.php';
?>

<style>
    /* Estilos específicos para el formulario */
    .admin-content {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-top: 1rem;
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .page-title {
        font-size: 1.8rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--dark-color);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }

    .input-group {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--dark-color);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary {
        background-color: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #7f8c8d;
        transform: translateY(-2px);
    }

    .photo-options {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .photo-option {
        flex: 1;
        text-align: center;
        padding: 1.5rem;
        border: 2px dashed #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .photo-option:hover {
        border-color: var(--secondary-color);
        background-color: rgba(52, 152, 219, 0.05);
    }

    .photo-option i {
        font-size: 2rem;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }

    .photo-option.active {
        border-color: var(--secondary-color);
        background-color: rgba(52, 152, 219, 0.1);
    }

    .photo-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--secondary-color);
        margin: 1rem auto;
        display: block;
    }

    .password-suggestion {
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .suggest-password {
        color: var(--secondary-color);
        cursor: pointer;
        font-weight: 600;
    }

    .suggest-password:hover {
        text-decoration: underline;
    }

    .facial-data-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }

    .facial-data-placeholder {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        color: #7f8c8d;
    }

    .facial-data-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
    }

    /* Modal para la cámara */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 2rem;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .close-modal {
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--dark-color);
    }

    .camera-container {
        position: relative;
        width: 100%;
        margin: 0 auto;
    }

    #video {
        width: 100%;
        border-radius: 8px;
        display: block;
    }

    #canvas {
        display: none;
    }

    .camera-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 3px solid var(--secondary-color);
        border-radius: 50%;
        pointer-events: none;
    }

    .camera-controls {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .camera-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .take-photo {
        background-color: var(--secondary-color);
        color: white;
        border: none;
    }

    .take-photo:hover {
        background-color: #2980b9;
    }

    .retake-photo {
        background-color: white;
        color: var(--dark-color);
        border: 1px solid #ddd;
    }

    .retake-photo:hover {
        background-color: #f5f5f5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .photo-options {
            flex-direction: column;
        }

        .modal-content {
            margin: 10% auto;
            width: 95%;
        }

        .camera-overlay {
            width: 150px;
            height: 150px;
        }
    }
</style>

<div class="main-content">
    <div class="admin-content">
        <div class="content-header">
            <h1 class="page-title">
                <?php echo ($modo === 'editar') ? 'Editar Administrador' : 'Registrar Nuevo Administrador'; ?>
            </h1>
            <a href="router_admin.php?page=listar_administradores" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="form-container">
            <form id="adminForm" method="POST" enctype="multipart/form-data"
                action="router_admin.php?page=<?php echo ($modo === 'editar') ? 'actualizar_administrador&id=' . $admin['id'] : 'guardar_administrador'; ?>">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required
                        value="<?php echo htmlspecialchars($admin['nombre'] ?? ''); ?>">

                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div style="display: flex;">
                        <input type="text" id="email" name="email" class="form-control"
                            value="<?php echo isset($admin['email']) ? explode('@', $admin['email'])[0] : ''; ?>"
                            style="border-top-right-radius: 0; border-bottom-right-radius: 0; flex: 1;" required>
                        <span style="background-color: #eee; padding: 0.75rem; border: 1px solid #ddd; border-left: none; border-top-right-radius: 6px; border-bottom-right-radius: 6px;">@ciudad.alert.bo</span>
                    </div>
                </div>

                <?php if ($modo !== 'editar'): ?>
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-suggestion">
                            <span>Sugerencia: </span>
                            <span id="passwordSuggestion" class="suggest-password">Generar contraseña segura</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Foto de Perfil</label>
                    <div class="photo-options">
                        <div class="photo-option" id="uploadOption">
                            <i class="fas fa-upload"></i>
                            <p>Subir foto</p>
                            <input type="file" id="fotoInput" name="foto_perfil" accept="image/*" style="display: none;">
                        </div>
                        <div class="photo-option" id="cameraOption">
                            <i class="fas fa-camera"></i>
                            <p>Tomar foto</p>
                        </div>
                    </div>

                    <img id="photoPreview"
                        class="photo-preview"
                        src="<?php echo !empty($admin['foto_perfil']) ? 'data:image/jpeg;base64,' . base64_encode($admin['foto_perfil']) : ''; ?>"
                        style="display: <?php echo !empty($admin['foto_perfil']) ? 'block' : 'none'; ?>;">
                </div>

                <div class="facial-data-section">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Registro de Datos Faciales</h3>
                    <div class="facial-data-placeholder">
                        <i class="fas fa-user-shield"></i>
                        <h4>Reconocimiento Facial</h4>
                        <p>Esta funcionalidad estará disponible próximamente</p>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Administrador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para la cámara -->
<div id="cameraModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tomar Foto de Perfil</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="camera-container">
            <video id="video" autoplay playsinline></video>
            <canvas id="canvas"></canvas>
            <div class="camera-overlay"></div>
        </div>
        <div class="camera-controls">
            <button type="button" class="camera-btn take-photo" id="takePhoto">
                <i class="fas fa-camera"></i> Tomar Foto
            </button>
            <button type="button" class="camera-btn retake-photo" id="retakePhoto" style="display: none;">
                <i class="fas fa-redo"></i> Volver a Tomar
            </button>
        </div>
    </div>
</div>

<script>
    // Generar contraseña sugerida
    document.getElementById('passwordSuggestion')?.addEventListener('click', function() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('password').value = password;
        this.textContent = password;
    });

    // Mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }

    // Elementos del formulario
    const cameraModal = document.getElementById('cameraModal');
    const cameraOption = document.getElementById('cameraOption');
    const uploadOption = document.getElementById('uploadOption');
    const closeModal = document.querySelector('.close-modal');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const takePhotoBtn = document.getElementById('takePhoto');
    const retakePhotoBtn = document.getElementById('retakePhoto');
    const fotoInput = document.getElementById('fotoInput');
    const photoPreview = document.getElementById('photoPreview');
    let stream = null;

    // Función para limpiar la foto existente
    function clearExistingPhoto() {
        fotoInput.value = '';
        photoPreview.src = '';
        photoPreview.style.display = 'none';
    }

    // Manejar la selección de foto desde archivo
    uploadOption.addEventListener('click', function() {
        // Limpiar foto existente si estamos en modo edición
        if (photoPreview.style.display === 'block') {
            if (confirm('¿Desea reemplazar la foto actual?')) {
                clearExistingPhoto();
                fotoInput.click();
            }
        } else {
            fotoInput.click();
        }
    });

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                photoPreview.src = event.target.result;
                photoPreview.style.display = 'block';

                // Resaltar opción seleccionada
                uploadOption.classList.add('active');
                cameraOption.classList.remove('active');
            }
            reader.readAsDataURL(file);
        }
    });

    // Modal de la cámara
    cameraOption.addEventListener('click', async function() {
        // Limpiar foto existente si estamos en modo edición
        if (photoPreview.style.display === 'block') {
            if (!confirm('¿Desea reemplazar la foto actual?')) {
                return;
            }
            clearExistingPhoto();
        }

        cameraModal.style.display = 'block';
        uploadOption.classList.remove('active');
        this.classList.add('active');

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                },
                audio: false
            });
            video.srcObject = stream;
            takePhotoBtn.style.display = 'flex';
            retakePhotoBtn.style.display = 'none';
        } catch (err) {
            console.error("Error al acceder a la cámara: ", err);
            alert("No se pudo acceder a la cámara. Asegúrese de haber concedido los permisos necesarios.");
            closeCamera();
        }
    });

    // Tomar foto
    takePhotoBtn.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        const videoWidth = video.videoWidth;
        const videoHeight = video.videoHeight;

        canvas.width = videoWidth;
        canvas.height = videoHeight;
        context.drawImage(video, 0, 0, videoWidth, videoHeight);

        // Crear máscara circular
        context.globalCompositeOperation = 'destination-in';
        context.beginPath();
        context.arc(videoWidth / 2, videoHeight / 2, Math.min(videoWidth, videoHeight) / 2, 0, 2 * Math.PI);
        context.fill();

        // Convertir a Blob
        canvas.toBlob(function(blob) {
            const previewUrl = URL.createObjectURL(blob);
            photoPreview.src = previewUrl;
            photoPreview.style.display = 'block';

            // Crear un File y asignarlo al input file
            const file = new File([blob], 'profile.png', {
                type: 'image/png'
            });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fotoInput.files = dataTransfer.files;

            // Cambiar botones
            takePhotoBtn.style.display = 'none';
            retakePhotoBtn.style.display = 'flex';

            // Detener la cámara
            closeCamera();
        }, 'image/png');
    });

    // Volver a tomar foto
    retakePhotoBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                },
                audio: false
            });
            video.srcObject = stream;
            takePhotoBtn.style.display = 'flex';
            retakePhotoBtn.style.display = 'none';
        } catch (err) {
            console.error("Error al acceder a la cámara: ", err);
            alert("No se pudo acceder a la cámara para volver a tomar la foto.");
        }
    });

    // Cerrar la cámara
    function closeCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    // Cerrar modal
    closeModal.addEventListener('click', function() {
        closeCamera();
        cameraModal.style.display = 'none';
    });

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        if (event.target === cameraModal) {
            closeCamera();
            cameraModal.style.display = 'none';
        }
    });

    // Validar formulario antes de enviar
    document.getElementById('adminForm').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();
        const hasPhoto = document.getElementById('fotoInput').files.length > 0 ||
            (photoPreview.style.display === 'block' && <?php echo ($modo === 'editar') ? 'true' : 'false'; ?>);

        <?php if ($modo !== 'editar'): ?>
            const password = document.getElementById('password').value;
            if (!nombre || !email || !password) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
                return false;
            }
        <?php else: ?>
            if (!nombre || !email) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
                return false;
            }
        <?php endif; ?>

        if (!hasPhoto) {
            e.preventDefault();
            alert('Por favor seleccione o tome una foto de perfil');
            return false;
        }

        if (!email.endsWith('@ciudad.alert.bo')) {
            document.getElementById('email').value = email + '@ciudad.alert.bo';
        }

        return true;
    });
</script>

<?php
ob_end_flush();
?>