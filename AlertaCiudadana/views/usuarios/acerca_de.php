<?php
require_once __DIR__ . "../../../templates/header.php";
?>
<style>
    /* Estilos específicos para la página Acerca de */
    .about-section {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .about-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .about-header h2 {
        color: var(--primary-color);
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .about-header p {
        color: #666;
        max-width: 800px;
        margin: 0 auto;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .about-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .about-card {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .about-card:hover {
        transform: translateY(-5px);
    }

    .about-icon {
        background: var(--secondary-color);
        color: white;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.8rem;
    }

    .about-card h3 {
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .about-card p {
        color: #666;
        line-height: 1.6;
    }

    .team-section {
        background: #f8f9fa;
        padding: 3rem 1rem;
        margin: 3rem 0;
        border-radius: 8px;
    }

    .team-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .team-container h2 {
        text-align: center;
        color: var(--primary-color);
        margin-bottom: 2rem;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .team-member {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .team-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .team-info {
        padding: 1.5rem;
    }

    .team-info h3 {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .team-info p {
        color: var(--secondary-color);
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .team-info .social-links {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .social-links a {
        color: var(--dark-color);
        font-size: 1.2rem;
        transition: color 0.3s;
    }

    .social-links a:hover {
        color: var(--secondary-color);
    }

    @media (max-width: 768px) {
        .about-header h2 {
            font-size: 2rem;
        }
    }
</style>

<div class="about-section">
    <div class="about-header">
        <h2>Sobre Alerta Ciudadana</h2>
        <p>Una plataforma innovadora creada para fortalecer la participación ciudadana y mejorar la calidad de vida en Santa Cruz de la Sierra. Conectamos a los ciudadanos con las autoridades para resolver problemas urbanos de manera eficiente.</p>
    </div>

    <div class="about-cards">
        <div class="about-card">
            <div class="about-icon">👁️</div>
            <h3>Nuestra Visión</h3>
            <p>Ser la plataforma líder en participación ciudadana en Bolivia, transformando la manera en que los ciudadanos interactúan con las autoridades para construir ciudades más limpias, seguras y eficientes.</p>
        </div>

        <div class="about-card">
            <div class="about-icon">🎯</div>
            <h3>Nuestra Misión</h3>
            <p>Facilitar la comunicación entre los ciudadanos y el gobierno municipal, proporcionando herramientas tecnológicas que permitan reportar, monitorear y resolver problemas urbanos de manera transparente y eficaz.</p>
        </div>

        <div class="about-card">
            <div class="about-icon">💡</div>
            <h3>Nuestros Valores</h3>
            <p>Transparencia, eficiencia, colaboración e innovación. Creemos en el poder de la comunidad para generar cambios positivos en su entorno.</p>
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto 3rem;">
        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 1.5rem;">¿Cómo surgió esta iniciativa?</h2>
        <p style="color: #666; line-height: 1.6; text-align: center;">Alerta Ciudadana nació en 2023 como respuesta a la necesidad de los cruceños de contar con un canal directo para reportar problemas urbanos. Frustrados por la falta de mecanismos eficientes, un grupo de jóvenes profesionales decidió crear esta plataforma que hoy conecta a miles de ciudadanos con las autoridades municipales.</p>
    </div>

    <div class="team-section">
        <div class="team-container">
            <h2>Nuestro Equipo</h2>

            <div class="team-grid">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Juan Pérez" class="team-img">
                    <div class="team-info">
                        <h3>Pablo Gutierrez</h3>
                        <p>Fundador & CEO</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="María Fernández" class="team-img">
                    <div class="team-info">
                        <h3>Jhonatan Mamani</h3>
                        <p>Director de Operaciones</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="../../../AlertaCiudadana/images/grupo/mauricio.jpeg" alt="Carlos Rojas" class="team-img">
                    <div class="team-info">
                        <h3>Mauricio Vasquez</h3>
                        <p>Desarrollador Principal</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="../../../AlertaCiudadana/images/grupo/juliana.jpeg" alt="Ana Mendoza" class="team-img">
                    <div class="team-info">
                        <h3>Juliana Rojas</h3>
                        <p>Relaciones Públicas</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="background: var(--light-color); padding: 2rem; border-radius: 8px; text-align: center;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">¿Quieres unirte a nuestro equipo?</h3>
        <p style="color: #666; margin-bottom: 1.5rem;">Estamos siempre buscando talento comprometido con mejorar nuestra ciudad.</p>
        <a href="<?php echo $base_url; ?>views/ciudadanos/contacto.php" class="btn btn-primary" style="padding: 0.8rem 2rem;">Contáctanos</a>
    </div>
</div>
<?php
require_once __DIR__ . "../../../templates/footer.php";
?>