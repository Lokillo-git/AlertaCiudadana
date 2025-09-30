<?php require_once __DIR__ . '/../config/database.php';

class DenunciaModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    //cargar todas las categorias
    public function obtenerCategorias()
    {
        try {
            $sql = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function guardarDenuncia($usuario_id, $categoria_id, $titulo, $descripcion, $ubicacion, $latitud, $longitud)
    {
        try {
            $sql = "INSERT INTO denuncias (usuario_id, categoria_id, titulo, descripcion, ubicacion, latitud, longitud)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario_id, $categoria_id, $titulo, $descripcion, $ubicacion, $latitud, $longitud]);
            return $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function guardarEvidencia($denuncia_id, $tipo, $archivo)
    {
        try {
            $sql = "INSERT INTO evidencias (denuncia_id, tipo, archivo_path) VALUES (:denuncia_id, :tipo, :archivo)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':denuncia_id', $denuncia_id, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);

            // Importante: Usar PARAM_LOB para datos binarios
            $stmt->bindParam(':archivo', $archivo, PDO::PARAM_LOB);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar evidencia: " . $e->getMessage());
            return false;
        }
    }
}
