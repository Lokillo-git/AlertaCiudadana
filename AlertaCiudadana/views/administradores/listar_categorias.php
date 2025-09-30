<?php
require_once __DIR__ . '../../../templates/layout_admin.php';
require_once __DIR__ . '../../../controllers/admin_controller.php';

$controller = new admin_controller();
$categorias = $controller->obtenerCategorias();
$categorias = $controller->obtenerTodasLasCategorias();
?>

<style>
    /* Estilos específicos para la página de categorías */
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

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .badge-category {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        background-color: #e0e0e0;
        color: #333;
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

    /* Estilos para el modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 500px;
        transform: translateY(-20px);
        transition: all 0.3s ease;
    }

    .modal-overlay.active .modal-container {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.3rem;
        color: var(--primary-color);
        font-weight: 600;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #7f8c8d;
        transition: color 0.3s;
    }

    .modal-close:hover {
        color: #e74c3c;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        outline: none;
    }

    .select-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        background-color: white;
        cursor: pointer;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
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

        .modal-container {
            width: 95%;
            margin: 0 auto;
        }
    }
</style>

<div class="main-content">
    <div class="admin-content">
        <div class="content-header">
            <h1 class="page-title">Categorías de Denuncias</h1>
            <button id="openModalBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Categoría
            </button>
        </div>

        <!-- Tabla de categorías -->
        <table id="categoriasTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Encargado</th>
                    <th>Teléfono Encargado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cat['id']); ?></td>
                        <td><?php echo htmlspecialchars($cat['nombre']); ?></td>
                        <td><?php echo !empty($cat['nombre_encargado']) ? htmlspecialchars($cat['nombre_encargado']) : 'Sin asignar'; ?></td>
                        <td><?php echo !empty($cat['telefono_encargado']) ? htmlspecialchars($cat['telefono_encargado']) : 'Sin asignar'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-edit" data-id="<?php echo $cat['id']; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button class="btn btn-danger">
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

<!-- Modal para nueva categoría -->
<div id="modalNuevaCategoria" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Nueva Categoría</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="formNuevaCategoria" method="POST" action="router_admin.php?page=guardar_categoria">
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombreCategoria">Nombre de la categoría</label>
                    <input type="text" name="nombre" id="nombreCategoria" class="form-control" required placeholder="Ej: Basura, Vandalismo, etc.">
                </div>
                <div class="form-group">
                    <label for="encargadoCategoria">Encargado</label>
                    <select name="encargado_id" id="encargadoCategoria" class="select-control" required>
                        <option value="">-- Seleccione un encargado --</option>
                        <option value="0">Sin asignar</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn modal-close">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <input type="hidden" name="categoria_id" id="categoriaIdInput">
        </form>
    </div>
</div>

<!-- Incluir jQuery y DataTables -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#categoriasTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            columnDefs: [{
                    orderable: false,
                    targets: [4]
                },
                {
                    searchable: false,
                    targets: [4]
                },
                {
                    width: "10%",
                    targets: 0
                },
                {
                    width: "25%",
                    targets: 1
                },
                {
                    width: "25%",
                    targets: 2
                },
                {
                    width: "20%",
                    targets: 3
                },
                {
                    width: "20%",
                    targets: 4
                }
            ],
            order: [
                [0, 'asc']
            ],
            responsive: true,
            dom: '<"top"<"dataTables_length"l><"dataTables_filter"f>><"table-responsive"rt><"bottom"<"dataTables_info"i><"dataTables_paginate"p>>'
        });

        // Control del modal
        const modal = $('#modalNuevaCategoria');
        const openModalBtn = $('#openModalBtn');
        const closeModalBtns = $('.modal-close');
        const nombreInput = $('#nombreCategoria');
        const encargadoSelect = $('#encargadoCategoria');
        const categoriaIdInput = $('#categoriaIdInput');

        // Abrir modal en modo CREAR
        openModalBtn.on('click', function() {
            $('#formNuevaCategoria')[0].reset(); // limpia campos
            encargadoSelect.empty().append(`<option value="">-- Seleccione un encargado --</option><option value="0">Sin asignar</option>`);
            categoriaIdInput.val(''); // asegúrate de que esté vacío
            modal.addClass('active');
        });

        // Cerrar modal
        closeModalBtns.on('click', function() {
            modal.removeClass('active');
        });

        modal.on('click', function(e) {
            if (e.target === modal[0]) {
                modal.removeClass('active');
            }
        });

        // Abrir modal en modo EDITAR
        $('.btn-edit').on('click', function() {
            const categoriaId = $(this).data('id');

            // AJAX para traer datos de la categoría
            $.ajax({
                url: `router_admin.php?page=datos_categoria&id=${categoriaId}`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    modal.addClass('active');

                    // Ajuste aquí: acceder al objeto "categoria"
                    nombreInput.val(data.categoria.nombre);
                    categoriaIdInput.val(data.categoria.id);

                    // Limpiar y recargar el select
                    encargadoSelect.empty();
                    encargadoSelect.append(`<option value="">-- Seleccione un encargado --</option>`);
                    encargadoSelect.append(`<option value="0">Sin asignar</option>`);

                    data.agentes.forEach(function(agente) {
                        const selected = (agente.id == data.categoria.encargado_id) ? 'selected' : '';
                        encargadoSelect.append(`<option value="${agente.id}" ${selected}>${agente.nombre}</option>`);
                    });
                },
                error: function() {
                    Swal.fire("Error", "No se pudo cargar la categoría", "error");
                }
            });
        });
    });
</script>