<?php
require_once __DIR__ . "../../../templates/header.php";
?>

<style>
    /* Estilos específicos para la página de contacto */
    .contact-section {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .contact-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .contact-info {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .contact-info h3 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .contact-icon {
        background: var(--secondary-color);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .contact-text h4 {
        margin-bottom: 0.3rem;
        color: var(--dark-color);
    }

    .contact-text p,
    .contact-text a {
        color: #666;
        text-decoration: none;
    }

    .contact-text a:hover {
        color: var(--secondary-color);
    }

    .contact-form {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    .submit-btn {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: background 0.3s;
    }

    .submit-btn:hover {
        background: var(--primary-color);
    }

    .map-container {
        margin-top: 2rem;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .contact-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="contact-section">
    <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 1rem;">Contáctanos</h2>
    <p style="text-align: center; max-width: 700px; margin: 0 auto; color: #666;">¿Tienes preguntas, sugerencias o necesitas ayuda? Estamos aquí para ayudarte.</p>

    <div class="contact-container">
        <div class="contact-info">
            <h3>Información de Contacto</h3>

            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-text">
                    <h4>Teléfono</h4>
                    <p><a href="tel:+59171391963">+591 71391963</a></p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">✉️</div>
                <div class="contact-text">
                    <h4>Email</h4>
                    <p><a href="mailto:contacto@ciudadalert.gob.bo">contacto@ciudadalert.gob.bo</a></p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">🏢</div>
                <div class="contact-text">
                    <h4>Oficina Central</h4>
                    <p>Calle Beni #456, Santa Cruz de la Sierra, Bolivia</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">🕒</div>
                <div class="contact-text">
                    <h4>Horario de Atención</h4>
                    <p>Lunes a Viernes: 8:00 - 18:00<br>Sábado: 9:00 - 12:00</p>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h3>Envíanos un Mensaje</h3>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono (Opcional)</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control">
                </div>

                <div class="form-group">
                    <label for="asunto">Asunto</label>
                    <select id="asunto" name="asunto" class="form-control" required>
                        <option value="">Seleccione un asunto</option>
                        <option value="consulta">Consulta General</option>
                        <option value="reporte">Problema con un Reporte</option>
                        <option value="sugerencia">Sugerencia</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" class="form-control" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Enviar Mensaje</button>
            </form>
        </div>
    </div>

    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3799.511255876968!2d-63.18678892468628!3d-17.78367389136675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTfCsDQ3JzAxLjIiUyA2M8KwMTEnMDYuNyJX!5e0!3m2!1ses!2sbo!4v1620000000000!5m2!1ses!2sbo" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<?php
require_once __DIR__ . "../../../templates/footer.php";
?>