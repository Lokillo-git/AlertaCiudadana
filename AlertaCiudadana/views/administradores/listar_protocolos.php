<?php
require_once __DIR__ . '../../../templates/layout_admin.php';
require_once __DIR__ . '../../../models/protocolo_model.php';
require_once __DIR__ . '../../../controllers/protocolo_controller.php';

$model = new ProtocoloModel();
$controller = new ProtocoloController();
$categorias = $model->obtenerCategorias();
$protocolos = $controller->listarProtocolos();
?>

<style>
    /* Estilos específicos para la página de protocolos */
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

    /* Estilos para los protocolos */
    .protocol-container {
        margin-bottom: 2.5rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        background-color: #f9f9f9;
    }

    .protocol-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .protocol-title {
        font-size: 1.4rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    .pasos-list {
        list-style-type: none;
        padding: 0;
        counter-reset: paso-counter;
    }

    .paso-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .paso-number {
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        background-color: var(--secondary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-weight: bold;
        position: relative;
        counter-increment: paso-counter;
    }

    .paso-number::before {
        content: counter(paso-counter);
    }

    .paso-content {
        flex-grow: 1;
    }

    .paso-actions {
        display: flex;
        gap: 0.5rem;
        margin-left: 1rem;
    }

    /* Formulario de nuevo protocolo */
    .form-protocolo {
        background-color: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
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

    .select-categoria {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        background-color: white;
        transition: all 0.3s;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .paso-item {
            flex-direction: column;
        }

        .paso-actions {
            margin-left: 0;
            margin-top: 1rem;
            justify-content: flex-end;
        }
    }
</style>

<div class="main-content">
    <div class="admin-content">
        <div class="content-header">
            <h1 class="page-title">Protocolos de Atención</h1>
            <button id="toggleFormBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Protocolo
            </button>
        </div>

        <!-- Formulario para agregar nuevo protocolo (inicialmente oculto) -->
        <div id="nuevoProtocoloForm" class="form-protocolo" style="display: none;">
            <h3>Agregar Nuevo Protocolo</h3>
            <form id="protocoloForm" action="router_admin.php?page=guardar_protocolo" method="POST">
                <div class="form-group">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select id="categoria" name="id_categoria" class="select-categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion_paso" class="form-label">Paso a seguir</label>
                    <textarea id="descripcion_paso" name="descripcion_paso" class="form-control" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="orden" class="form-label">Orden del paso</label>
                    <input type="number" id="orden" name="orden" class="form-control" min="1" required>
                </div>

                <div style="text-align: right;">
                    <button type="button" id="cancelFormBtn" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Paso
                    </button>
                </div>
            </form>
        </div>

        <!-- Listado de protocolos por categoría -->
        <div class="protocolos-list">
            <?php if (empty($protocolos)): ?>
                <div class="alert alert-info">
                    No hay protocolos registrados. Agregue el primer protocolo usando el botón superior.
                </div>
            <?php else: ?>
                <?php foreach ($protocolos as $categoria): ?>
                    <div class="protocol-container">
                        <div class="protocol-header">
                            <h3 class="protocol-title"><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
                            <button class="btn btn-primary btn-agregar-paso" data-categoria="<?php echo $categoria['id']; ?>">
                                <i class="fas fa-plus"></i> Agregar Paso
                            </button>
                        </div>

                        <?php if (empty($categoria['pasos'])): ?>
                            <p>Esta categoría no tiene pasos definidos aún.</p>
                        <?php else: ?>
                            <ol class="pasos-list">
                                <?php foreach ($categoria['pasos'] as $paso): ?>
                                    <li class="paso-item">
                                        <div class="paso-number"></div>
                                        <div class="paso-content">
                                            <?php echo htmlspecialchars($paso['descripcion_paso']); ?>
                                        </div>
                                        <div class="paso-actions">
                                            <button class="btn btn-edit btn-editar-paso"
                                                data-id="<?php echo $paso['id_paso']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($paso['descripcion_paso']); ?>"
                                                data-orden="<?php echo $paso['orden']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-eliminar-paso"
                                                data-id="<?php echo $paso['id_paso']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($paso['descripcion_paso']); ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para editar paso (oculto inicialmente) -->
<div id="editarPasoModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px; margin: 5% auto;">
        <div class="modal-header">
            <h3>Editar Paso</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editarPasoForm" method="POST" action="router_admin.php?page=actualizar_paso">
                <input type="hidden" id="editar_id_paso" name="id_paso">
                <div class="form-group">
                    <label for="editar_descripcion" class="form-label">Descripción del paso</label>
                    <textarea id="editar_descripcion" name="descripcion_paso" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editar_orden" class="form-label">Orden</label>
                    <input type="number" id="editar_orden" name="orden" class="form-control" min="1" required>
                </div>
                <div style="text-align: right; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary cerrar-modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluir jQuery y SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Mostrar/ocultar formulario nuevo protocolo
        $('#toggleFormBtn').click(function() {
            $('#nuevoProtocoloForm').slideToggle();
        });

        $('#cancelFormBtn').click(function() {
            $('#nuevoProtocoloForm').slideUp();
        });

        // Botón agregar paso específico para una categoría
        $('.btn-agregar-paso').click(function() {
            const categoriaId = $(this).data('categoria');
            $('#categoria').val(categoriaId);
            $('#nuevoProtocoloForm').slideDown();
            $('html, body').animate({
                scrollTop: $('#nuevoProtocoloForm').offset().top - 20
            }, 500);
        });

        // Editar paso
        $('.btn-editar-paso').click(function() {
            const id = $(this).data('id');
            const descripcion = $(this).data('descripcion');
            const orden = $(this).data('orden');

            $('#editar_id_paso').val(id);
            $('#editar_descripcion').val(descripcion);
            $('#editar_orden').val(orden);

            $('#editarPasoModal').show();
        });

        // Eliminar paso
        $('.btn-eliminar-paso').click(function() {
            const id = $(this).data('id');
            const descripcion = $(this).data('descripcion');

            Swal.fire({
                title: '¿Eliminar paso?',
                html: `¿Estás seguro de eliminar el paso: <b>"${descripcion}"</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('router_admin.php?page=eliminar_paso', {
                            id_paso: id
                        })
                        .done(function(response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    'El paso ha sido eliminado.',
                                    'success'
                                ).then(() => location.reload());
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.message || 'No se pudo eliminar el paso',
                                    'error'
                                );
                            }
                        })
                        .fail(function() {
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al intentar eliminar el paso',
                                'error'
                            );
                        });
                }
            });
        });

        // Cerrar modal
        $('.close-modal, .cerrar-modal').click(function() {
            $('#editarPasoModal').hide();
        });

        // Cerrar modal al hacer clic fuera
        $(window).click(function(event) {
            if (event.target.id === 'editarPasoModal') {
                $('#editarPasoModal').hide();
            }
        });
    });
</script>