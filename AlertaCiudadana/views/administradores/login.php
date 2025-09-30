<?php
ob_start(); // Inicia el buffer de salida para manejar errores y mensajes
require_once __DIR__ . '../../../templates/header.php';
require_once __DIR__ . '../../../controllers/admin_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new admin_controller();
    $controller->iniciarSesion();
}
?>

<style>
    /* Estilos base del login */
    .login-section {
        max-width: 500px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .login-card {
        background: white;
        border-radius: 8px;
        padding: 2.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-header h2 {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .login-header p {
        color: #666;
    }

    .form-group {
        margin-bottom: 1.5rem;
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

    .btn {
        width: 100%;
        padding: 0.8rem;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: var(--secondary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-color);
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #333;
        margin-top: 1rem;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
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

    /* Estilos para el selector de rol */
    .role-selector {
        display: flex;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    .role-option {
        flex: 1;
        text-align: center;
        padding: 0.8rem;
        cursor: pointer;
        transition: all 0.3s;
        background: #f9f9f9;
    }

    .role-option:first-child {
        border-right: 1px solid #ddd;
    }

    .role-option.active {
        background: var(--secondary-color);
        color: white;
    }

    /* Estilos para la sección de reconocimiento facial */
    .facial-section {
        margin: 1.5rem 0;
        text-align: center;
    }

    .facial-placeholder {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
    }

    .facial-placeholder i {
        font-size: 2.5rem;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }

    .facial-placeholder p {
        color: #666;
        margin: 0;
    }

    .coming-soon {
        font-size: 0.8rem;
        color: #999;
        font-style: italic;
    }

    /* Mensajes de error */
    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 1.5rem;
        }
    }

    .alert-error {
        display: flex;
        align-items: center;
        background-color: #ffe0e0;
        color: #a94442;
        border: 1px solid #f5c6cb;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 15px;
        font-size: 0.95rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .alert-error i {
        margin-right: 10px;
        font-size: 1.2rem;
        color: #d9534f;
    }
</style>

<div class="login-section">
    <div class="login-card">
        <div class="login-header">
            <h2>Acceso al Sistema</h2>
            <p>Selecciona tu tipo de usuario e inicia sesión</p>
        </div>

        <?php
        if (isset($_SESSION['error_login'])) {
            echo '
    <div class="alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span>' . htmlspecialchars($_SESSION['error_login']) . '</span>
    </div>';
            unset($_SESSION['error_login']);
        }
        ?>

        <form method="POST">
            <!-- Selector de Rol -->
            <div class="role-selector">
                <div class="role-option active" id="adminOption" onclick="selectRole('admin')">
                    <i class="fas fa-user-shield"></i>
                    <p>Administrador</p>
                </div>
                <div class="role-option" id="workerOption" onclick="selectRole('worker')">
                    <i class="fas fa-user-tie"></i>
                    <p>Trabajador</p>
                </div>
                <input type="hidden" id="userRole" name="user_role" value="admin">
            </div>

            <!-- Campos de login -->
            <div class="form-group">
                <label for="email">Correo Institucional</label>
                <input type="email" id="email" name="email" class="form-control"
                    placeholder="usuario@ciudad.alert.bo" required
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Ingresa tu contraseña" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
                </div>
            </div>

            <!-- Sección de reconocimiento facial (no funcional) -->
            <div class="facial-section">
                <div class="facial-placeholder" onclick="alert('Función de reconocimiento facial estará disponible próximamente')">
                    <i class="fas fa-user-circle"></i>
                    <p>Reconocimiento Facial</p>
                    <small class="coming-soon">(Próximamente)</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</div>

<script>
    // Selección de rol
    function selectRole(role) {
        document.getElementById('userRole').value = role;

        if (role === 'admin') {
            document.getElementById('adminOption').classList.add('active');
            document.getElementById('workerOption').classList.remove('active');
        } else {
            document.getElementById('workerOption').classList.add('active');
            document.getElementById('adminOption').classList.remove('active');
        }
    }

    // Mostrar/ocultar contraseña
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.textContent = '👁️‍🗨️';
        } else {
            passwordInput.type = 'password';
            toggleIcon.textContent = '👁️';
        }
    }
</script>

<?php ob_end_flush(); // Asegura que el contenido se envíe correctamente 
?>