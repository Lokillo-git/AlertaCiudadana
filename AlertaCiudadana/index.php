<?php
require_once __DIR__ . "../../AlertaCiudadana/templates/header.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CiudadAlert - Santa Cruz de la Sierra</title>
    <style>
        /* Estilos generales */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            padding: 4rem 0;
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .text-center {
            text-align: center;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(52, 73, 94, 0.9)),
                url('https://images.unsplash.com/photo-1581431886217-927764b8ad4a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        /* How it works */
        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .step-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .step-card:hover {
            transform: translateY(-5px);
        }

        .step-number {
            background: var(--secondary-color);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 1.5rem;
        }

        /* Common Problems */
        .problems-section {
            background-color: #f8f9fa;
        }

        .problems-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .problem-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .problem-img {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }

        .problem-content {
            padding: 1.5rem;
        }

        /* Testimonials */
        .testimonials {
            background-color: var(--light-color);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1rem;
        }

        .testimonial-author {
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>¡Tu voz hace la diferencia en Santa Cruz!</h1>
            <p>Reporta problemas en la ciudad y ayúdanos a mejorar nuestro espacio urbano. Juntos podemos construir una Santa Cruz más segura y limpia.</p>
            <div class="hero-buttons">
                <a href="router.php?page=reportar" class="btn btn-primary">Reportar un problema</a>
                <a href="router.php?page=listar_denuncias_usuarios" class="btn btn-outline">Ver denuncias</a>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="container">
        <h2>¿Cómo funciona Alerta Ciudadana?</h2>

        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Registra tu denuncia</h3>
                <p>Describe el problema que encontraste en la ciudad y sube fotos como evidencia.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Validamos la información</h3>
                <p>Nuestro equipo verifica la denuncia y la clasifica según su urgencia y área responsable.</p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Seguimiento y solución</h3>
                <p>Derivamos el reporte a la entidad correspondiente y monitoreamos su resolución.</p>
            </div>
        </div>
    </section>

    <!-- Common Problems in Santa Cruz -->
    <section class="problems-section">
        <div class="container">
            <h2>Problemas comunes en Santa Cruz</h2>
            <p class="text-center" style="max-width: 800px; margin: 0 auto 2rem;">Estos son los problemas que más afectan a nuestra ciudad y que puedes reportar:</p>

            <div class="problems-grid">
                <div class="problem-card">
                    <img src="../AlertaCiudadana/images/inicio/basura.png" alt="Basura en las calles" class="problem-img">
                    <div class="problem-content">
                        <h3>Basura en las calles</h3>
                        <p>Vertederos ilegales, contenedores desbordados y acumulación de residuos en espacios públicos.</p>
                    </div>
                </div>

                <div class="problem-card">
                    <img src="../AlertaCiudadana/images/inicio/calles.png" alt="Calles en mal estado" class="problem-img">
                    <div class="problem-content">
                        <h3>Calles en mal estado</h3>
                        <p>Baches, pavimento deteriorado y falta de mantenimiento en vías públicas.</p>
                    </div>
                </div>

                <div class="problem-card">
                    <img src="../AlertaCiudadana/images/inicio/inundaciones.png" alt="Inundaciones" class="problem-img">
                    <div class="problem-content">
                        <h3>Inundaciones</h3>
                        <p>Drenajes obstruidos y zonas con acumulación de agua en temporada de lluvias.</p>
                    </div>
                </div>

                <div class="problem-card">
                    <img src="../AlertaCiudadana/images/inicio/alumbrado.png" alt="Alumbrado público" class="problem-img">
                    <div class="problem-content">
                        <h3>Alumbrado público</h3>
                        <p>Postes de luz dañados, focos quemados y zonas con poca iluminación nocturna.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <h2>Lo que dicen nuestros ciudadanos</h2>

            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">"Gracias a Alerta Ciudadana, el bache peligroso frente a mi casa fue reparado en solo 3 días. ¡Excelente servicio!"</p>
                    <p class="testimonial-author">- Juan Pérez, Barrio Equipetrol</p>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">"Reporté la acumulación de basura en mi zona y al día siguiente vinieron a limpiar. Muy satisfecha con los resultados."</p>
                    <p class="testimonial-author">- María Fernández, Distrito 5</p>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">"Como administrador, esta plataforma me ayuda a priorizar los problemas más urgentes de la ciudad. Muy útil."</p>
                    <p class="testimonial-author">- Carlos Rojas, Funcionario Municipal</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="background-color: var(--primary-color); color: white; padding: 4rem 0;">
        <div class="container text-center">
            <h2 style="color: white;">¿Listo para mejorar tu ciudad?</h2>
            <p style="max-width: 700px; margin: 0 auto 2rem;">Regístrate ahora y comienza a reportar los problemas que encuentres en Santa Cruz de la Sierra.</p>
            <a href="#" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Comenzar ahora</a>
        </div>
    </section>
</body>

</html>

<?php
require_once __DIR__ . "../../AlertaCiudadana/templates/footer.php";
?>