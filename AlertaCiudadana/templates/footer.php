<footer style="background: linear-gradient(135deg, var(--primary-color), var(--dark-color)); color: white; padding: 2rem 0; margin-top: 2rem;">
    <div class="footer-container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
        <!-- Sección 1: Logo y descripción -->
        <div class="footer-section">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Ciudad<span style="color: var(--secondary-color);">Alert</span></h3>
            <p style="line-height: 1.6; opacity: 0.8;">Plataforma para reportar problemas en tu ciudad y contribuir a mejorar nuestro entorno.</p>
        </div>

        <!-- Sección 2: Enlaces rápidos -->
        <div class="footer-section">
            <h4 style="font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Enlaces Rápidos</h4>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 0.5rem;"><a href="index.php" style="color: white; text-decoration: none; opacity: 0.8; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">Inicio</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="router.php?page=reportar" style="color: white; text-decoration: none; opacity: 0.8; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">Reportar Problema</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="router.php?page=denuncias" style="color: white; text-decoration: none; opacity: 0.8; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">Denuncias Recientes</a></li>
                <li style="margin-bottom: 0.5rem;"><a href="router.php?page=contacto" style="color: white; text-decoration: none; opacity: 0.8; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">Cómo funciona</a></li>
            </ul>
        </div>

        <!-- Sección 3: Contacto -->
        <div class="footer-section">
            <h4 style="font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Contacto</h4>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="opacity: 0.8;">✉</span>
                    <span style="opacity: 0.8;">contacto@ciudadalert.gob</span>
                </li>
                <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="opacity: 0.8;">📞</span>
                    <span style="opacity: 0.8;">+123 456 7890</span>
                </li>
                <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="opacity: 0.8;">🏢</span>
                    <span style="opacity: 0.8;">Gobernación, Ciudad</span>
                </li>
            </ul>
        </div>

        <!-- Sección 4: Redes sociales y legal -->
        <div class="footer-section">
            <h4 style="font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Síguenos</h4>
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <a href="#" style="color: white; text-decoration: none; background: rgba(255,255,255,0.1); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">f</a>
                <a href="#" style="color: white; text-decoration: none; background: rgba(255,255,255,0.1); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">t</a>
                <a href="#" style="color: white; text-decoration: none; background: rgba(255,255,255,0.1); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">in</a>
            </div>

            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                <a href="#" style="color: white; text-decoration: none; font-size: 0.8rem; opacity: 0.7; margin-right: 1rem;">Términos</a>
                <a href="#" style="color: white; text-decoration: none; font-size: 0.8rem; opacity: 0.7;">Privacidad</a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div style="text-align: center; padding-top: 2rem; margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.9rem; opacity: 0.7;">
        © <?php echo date('Y'); ?> CiudadAlert. Todos los derechos reservados.
    </div>
</footer>