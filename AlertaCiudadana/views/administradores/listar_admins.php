<?php
require_once __DIR__ . '../../../templates/layout_admin.php';
require_once __DIR__ . '../../../controllers/admin_controller.php';
$controller = new admin_controller();
$administradores = $controller->listarAdministradores();
?>

<style>
    /* Estilos específicos para la página de administradores */
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

    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
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

    .btn-edit {
        background-color: #f39c12;
        color: white;
    }

    .btn-edit:hover {
        background-color: #d35400;
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: var(--accent-color);
        color: white;
    }

    .btn-danger:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }

    /* Estilos para DataTables personalizados */
    .dataTables_wrapper {
        background-color: white;
        border-radius: 8px;
        padding: 1rem 0;
    }

    .dataTables_filter input {
        border: 1px solid #ddd !important;
        padding: 0.5rem 0.75rem !important;
        border-radius: 6px !important;
        margin-left: 0.5rem;
    }

    .dataTables_length select {
        border: 1px solid #ddd !important;
        padding: 0.4rem 0.75rem !important;
        border-radius: 6px !important;
    }

    table.dataTable {
        border-collapse: collapse !important;
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
        width: 100% !important;
    }

    table.dataTable thead th {
        background-color: var(--primary-color) !important;
        color: white !important;
        border-bottom: none !important;
        padding: 1rem 0.75rem !important;
        font-weight: 500;
    }

    table.dataTable tbody td {
        padding: 0.75rem !important;
        vertical-align: middle !important;
        border-top: 1px solid #f0f0f0 !important;
        text-align: center;
    }

    table.dataTable tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05) !important;
    }

    .profile-picture-table {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--secondary-color);
    }

    .profile-default-table {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--light-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-color);
        font-size: 1rem;
        border: 2px solid var(--secondary-color);
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.9rem !important;
        border: 1px solid #ddd !important;
        border-radius: 6px !important;
        margin-left: 0.25rem !important;
        transition: all 0.3s;
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--secondary-color) !important;
        color: white !important;
        border: none !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: #e0e0e0 !important;
        border: 1px solid #ddd !important;
    }

    /* Estilos para alinear controles de DataTables */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        display: inline-block;
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_filter {
        float: right;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: inline-block;
        margin-top: 1rem;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: right;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: block;
            width: 100%;
            float: none;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: block;
            width: 100%;
            float: none;
            text-align: center;
            margin-top: 0.5rem;
        }
    }
</style>

<div class="main-content">
    <div class="admin-content">
        <div class="content-header">
            <h1 class="page-title">Administradores</h1>
            <a href="router_admin.php?page=nuevo_administrador" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Administrador
            </a>
        </div>

        <!-- Tabla de administradores -->
        <table id="administradoresTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($administradores as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['id']); ?></td>
                        <td>
                            <?php if (!empty($admin['foto_perfil'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($admin['foto_perfil']); ?>" class="profile-picture-table" alt="Foto perfil">
                            <?php else: ?>
                                <div class="profile-default-table">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($admin['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['fecha_creacion']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-edit" data-id="<?php echo $admin['id']; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-danger btn-delete-admin" data-id="<?php echo $admin['id']; ?>">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Incluir jQuery y DataTables -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#administradoresTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            columnDefs: [{
                    orderable: false,
                    targets: [1, 5]
                }, // Deshabilitar ordenación para foto y acciones
                {
                    searchable: false,
                    targets: [1, 5]
                }, // Deshabilitar búsqueda para foto y acciones
                {
                    width: "5%",
                    targets: 0
                }, // ID
                {
                    width: "10%",
                    targets: 1
                }, // Foto
                {
                    width: "25%",
                    targets: 2
                }, // Nombre
                {
                    width: "25%",
                    targets: 3
                }, // Email
                {
                    width: "15%",
                    targets: 4
                }, // Fecha creación
                {
                    width: "20%",
                    targets: 5
                } // Acciones
            ],
            order: [
                [0, 'asc']
            ], // Ordenar por ID ascendente por defecto
            responsive: true,
            dom: '<"top"<"dataTables_length"l><"dataTables_filter"f>><"table-responsive"rt><"bottom"<"dataTables_info"i><"dataTables_paginate"p>>'
        });
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            window.location.href = `router_admin.php?page=editar_administrador&id=${id}`;
        });
    });

    document.querySelectorAll('.btn-delete-admin').forEach(button => {
        button.addEventListener('click', function() {
            const adminId = this.getAttribute('data-id');

            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡Esta acción no se puede deshacer!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('router_admin.php?page=eliminar_admin', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id=${adminId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("¡Eliminado!", "El administrador ha sido eliminado.", "success")
                                    .then(() => location.reload());
                            } else {
                                Swal.fire("Error", "No se pudo eliminar el administrador.", "error");
                            }
                        });
                }
            });
        });
    });
</script>