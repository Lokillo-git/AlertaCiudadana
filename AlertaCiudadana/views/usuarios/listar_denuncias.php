<?php
require_once __DIR__ . '../../../templates/header.php';
require_once __DIR__ . '../../../controllers/usuario_controller.php';

// Instanciar controlador
$usuarioController = new UsuarioController();
$denuncias = $usuarioController->obtenerDenunciasParaUsuario();

// Manejar errores
if (isset($denuncias['error'])) {
    echo '<div class="alert alert-danger">' . $denuncias['error'] . '</div>';
    exit;
}

?>

<div class="denuncias-container">
    <h1 class="denuncias-title">Mis Denuncias</h1>

    <?php if (empty($denuncias)): ?>
        <div class="no-denuncias">
            <i class="fas fa-info-circle"></i>
            <p>No has realizado ninguna denuncia aún.</p>
            <a href="router.php?page=reportar" class="btn-reportar">Realizar una denuncia</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="denuncias-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Agente Asignado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($denuncias as $denuncia): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($denuncia['fecha_creacion'])) ?></td>
                            <td><?= htmlspecialchars($denuncia['titulo']) ?></td>
                            <td><?= htmlspecialchars($denuncia['categoria_nombre']) ?></td>
                            <td>
                                <?= $denuncia['agente_nombre'] ? htmlspecialchars($denuncia['agente_nombre']) : 'Sin asignar' ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= str_replace('_', '-', $denuncia['estado']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $denuncia['estado'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="router.php?page=detalles_denuncia&id=<?= $denuncia['denuncia_id'] ?>" class="btn-ver">
                                    <i class="fas fa-eye"></i> Ver estado
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    .denuncias-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .denuncias-title {
        color: #2c3e50;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        border-bottom: 2px solid #3498db;
        padding-bottom: 0.5rem;
    }

    .no-denuncias {
        text-align: center;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .no-denuncias i {
        font-size: 3rem;
        color: #3498db;
        margin-bottom: 1rem;
    }

    .no-denuncias p {
        font-size: 1.1rem;
        color: #495057;
        margin-bottom: 1.5rem;
    }

    .btn-reportar {
        display: inline-block;
        background-color: #3498db;
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        transition: background-color 0.3s;
    }

    .btn-reportar:hover {
        background-color: #2980b9;
    }

    .table-responsive {
        overflow-x: auto;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .denuncias-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    .denuncias-table th,
    .denuncias-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }

    .denuncias-table th {
        background-color: #2c3e50;
        color: white;
        font-weight: 600;
    }

    .denuncias-table tr:hover {
        background-color: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-pendiente {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-en-proceso {
        background-color: #cce5ff;
        color: #004085;
    }

    .status-resuelto {
        background-color: #d4edda;
        color: #155724;
    }

    .btn-ver {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #3498db;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: background-color 0.3s;
    }

    .btn-ver:hover {
        background-color: #2980b9;
    }

    @media (max-width: 768px) {
        .denuncias-table {
            display: block;
        }

        .denuncias-table thead {
            display: none;
        }

        .denuncias-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .denuncias-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
        }

        .denuncias-table td::before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
        }
    }
</style>

<script>
    // Hacer la tabla responsiva en móviles
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.querySelector('.denuncias-table');
        if (window.innerWidth <= 768) {
            const headers = table.querySelectorAll('th');
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (headers[index]) {
                        cell.setAttribute('data-label', headers[index].textContent);
                    }
                });
            });
        }
    });
</script>

<?php require_once __DIR__ . '../../../templates/footer.php'; ?>