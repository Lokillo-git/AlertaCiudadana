<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f7fa;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            color: white;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            z-index: 100;
        }

        .admin-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .profile-picture {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--secondary-color);
            margin-bottom: 1rem;
        }

        .profile-default {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            font-size: 2rem;
            border: 3px solid var(--secondary-color);
            margin-bottom: 1rem;
        }

        .profile-info {
            text-align: center;
        }

        .profile-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }

        .profile-email {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        /* Menú */
        .admin-menu {
            padding: 1rem 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            color: #f0f0f0;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 4px;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .menu-item.active {
            font-weight: 600;
            color: #ffffff;
        }

        .submenu {
            padding-left: 2.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: rgba(0, 0, 0, 0.1);
        }

        .submenu.show {
            max-height: 500px;
        }

        .submenu-item {
            display: block;
            padding: 0.6rem 1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
            color: #f0f0f0;
            /* Texto más claro */
            text-decoration: none;
            border-radius: 4px;
        }

        .submenu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            /* Un poco más visible en hover */
            color: #ffffff;
            /* Letra más blanca al pasar el mouse */
        }

        /* Contenido principal */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            background-color: #f5f7fa;
            min-height: 100vh;
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
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-header">
            <div class="profile-container">
                <?php if (!empty($_SESSION['usuario_foto_base64'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($_SESSION['usuario_foto_base64']); ?>" alt="Foto de perfil" class="profile-picture">
                <?php else: ?>
                    <div class="profile-default">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>

                <div class="profile-info">
                    <div class="profile-name"><?php echo htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador'); ?></div>
                    <div class="profile-email"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'admin@ejemplo.com'); ?></div>
                </div>
            </div>
        </div>

        <nav class="admin-menu">
            <!-- Dashboard -->
            <a href="router_admin.php?page=home" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>

            <!-- Registros -->
            <div class="menu-item">
                <i class="fas fa-database"></i>
                <span>Registros</span>
                <i class="fas fa-chevron-down ml-auto"></i>
            </div>
            <div class="submenu">
                <a href="router_admin.php?page=listar_trabajadores" class="submenu-item">Trabajadores</a>
                <a href="router_admin.php?page=listar_administradores" class="submenu-item">Administradores</a>
                <a href="router_admin.php?page=listar_protocolos" class="submenu-item">Protocolos</a>
                <a href="router_admin.php?page=listar_categorias" class="submenu-item">Categorías</a>
            </div>

            <!-- Informes -->
            <div class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Informes</span>
                <i class="fas fa-chevron-down ml-auto"></i>
            </div>
            <div class="submenu">
                <div class="submenu-item">Reportes</div>
            </div>

            <!-- Cerrar sesión -->
            <div class="logout-container" style="margin-top: 20px; padding: 10px;">
                <a href="logout.php" class="logout-btn" style="
                    display: block;
                    background: #d9534f;
                    color: white;
                    text-align: center;
                    padding: 10px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-weight: bold;
                ">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </nav>
    </aside>

    <script>
        document.querySelectorAll('.menu-item').forEach(menuItem => {
            menuItem.addEventListener('click', function(e) {
                const submenu = this.nextElementSibling;
                const isSubmenuClick = e.target.classList.contains('submenu-item');
                const icon = this.querySelector('.fa-chevron-down, .fa-chevron-up');

                // Manejar submenús
                if (submenu?.classList.contains('submenu') && !isSubmenuClick) {
                    submenu.classList.toggle('show');

                    // Cambiar dirección de flecha
                    if (icon) {
                        icon.classList.toggle('fa-chevron-down');
                        icon.classList.toggle('fa-chevron-up');
                    }
                    return;
                }

                // Marcar como activo solo si no es clic en submenú
                if (!isSubmenuClick) {
                    document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Selección visual al hacer clic en un submenu
        document.querySelectorAll('.submenu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                const parentMenuItem = this.closest('.submenu').previousElementSibling;
                parentMenuItem.classList.add('active');
            });
        });
    </script>
</body>

</html>