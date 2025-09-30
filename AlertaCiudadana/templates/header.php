<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CiudadAlert - Reporta problemas en tu ciudad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
        }

        .logo {
            flex-shrink: 0;
        }

        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .logo span {
            color: var(--secondary-color);
        }

        nav {
            flex-grow: 1;
            display: flex;
            justify-content: center;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 15px;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        nav li {
            white-space: nowrap;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .login-buttons {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-accent {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-accent:hover {
            background-color: #c0392b;
        }

        /* Estilos para el perfil de usuario */
        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary-color);
        }

        .profile-default {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            font-size: 1.2rem;
            border: 2px solid var(--secondary-color);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-email {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }

        .dropdown-menu a {
            color: var(--dark-color);
            padding: 0.8rem 1rem;
            display: block;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .dropdown-menu a:hover {
            background-color: #f5f5f5;
            color: var(--secondary-color);
        }

        .dropdown-menu.show {
            display: block;
        }

        @media (max-width: 1024px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .login-buttons,
            .user-profile-container {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Ciudad<span>Alert</span></h1>
            </div>

            <nav>
                <ul>
                    <li><a href="router.php?page=home">Inicio</a></li>
                    <li><a href="router.php?page=reportar">Reportar Problema</a></li>
                    <li><a href="router.php?page=acerca_de">Acerca de</a></li>
                    <li><a href="router.php?page=contacto">Contacto</a></li>
                </ul>
            </nav>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="user-profile-container">
                    <div class="user-profile" id="userProfile">
                        <?php
                        // Mostrar foto de perfil - versión simplificada
                        if (!empty($_SESSION['usuario_foto_base64'])) {
                            echo '<img src="' . htmlspecialchars($_SESSION['usuario_foto_base64']) . '" alt="Foto de perfil" class="profile-picture">';
                        } else {
                            echo '<div class="profile-default"><i class="fas fa-user"></i></div>';
                        }
                        ?>

                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></span>
                            <span class="user-email"><?php echo htmlspecialchars($_SESSION['usuario_email'] ?? 'Email no disponible'); ?></span>
                        </div>

                        <div class="dropdown-menu" id="dropdownMenu">
                            <a href="router.php?page=listar_denuncias_usuarios"><i class="fas fa-list"></i> Ver mis denuncias</a>
                            <a href="router.php?page=editar_perfil"><i class="fas fa-user-edit"></i> Editar perfil</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Mostrar botones de login cuando no está logueado -->
                <div class="login-buttons">
                    <a href="router.php?page=login"><button class="btn btn-primary">Iniciar sesión como ciudadano</button></a>
                    <a href="router_admin.php?page=login_admin"><button class="btn btn-accent">Iniciar sesión como administrador</button></a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <script>
        // Mostrar/ocultar el menú desplegable
        document.getElementById('userProfile').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('show');
        });

        // Cerrar el menú al hacer clic en cualquier parte de la página
        document.addEventListener('click', function() {
            const dropdown = document.getElementById('dropdownMenu');
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        });
    </script>