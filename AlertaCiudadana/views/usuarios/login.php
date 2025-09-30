<?php
// Incluir el controlador para iniciar sesión
require_once __DIR__ . "../../../controllers/usuario_controller.php";

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Crear una instancia del controlador
    $usuarioController = new UsuarioController();
    
    // Llamar al método iniciarSesion del controlador
    $usuarioController->iniciarSesion();
}

require_once __DIR__ . "../../../templates/header.php";
?>

<style>
    /* Estilos específicos para la página de login */
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

    .login-btn {
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

    .login-btn:hover {
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
        .login-card {
            padding: 1.5rem;
        }
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Estilo para el contenedor de errores */
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
</style>

<div class="login-section">
    <div class="login-card">
        <div class="login-header">
            <h2>Iniciar Sesión</h2>
            <p>Accede a tu cuenta de CiudadAlert</p>
        </div>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <p><?php echo $_SESSION['error_message']; ?></p>
            </div>
            <?php unset($_SESSION['error_message']); ?> <!-- Limpiar el mensaje después de mostrarlo -->
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control"
                    placeholder="tucorreo@ejemplo.com" required
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

            <div class="form-group" style="text-align: right;">
                <a href="#" style="font-size: 0.9rem; color: var(--secondary-color);">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="login-btn">Iniciar Sesión</button>

            <div class="login-footer">
                <p>¿No tienes una cuenta? <a href="router.php?page=sign_up">Regístrate aquí</a></p>
            </div>
        </form>
    </div>
</div>

<script>
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

<?php
require_once __DIR__ . "../../../templates/footer.php";
?>