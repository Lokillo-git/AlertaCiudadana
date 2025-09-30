<?php require_once __DIR__ . '/../config/database.php';

class ProtocoloModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    //Obtener todas las categorías de denuncias
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

    // Registrar un nuevo paso de protocolo para una categoría
    public function registrarPasoProtocolo($id_categoria, $descripcion_paso, $orden)
    {
        try {
            $sql = "INSERT INTO pasos_denuncia (id_categoria, descripcion_paso, orden) VALUES (?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([$id_categoria, $descripcion_paso, $orden]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerCategoriasConPasos()
    {
        try {
            // Obtener todas las categorías
            $sqlCategorias = "SELECT id, nombre FROM categorias";
            $stmtCat = $this->conexion->prepare($sqlCategorias);
            $stmtCat->execute();
            $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

            // Obtener los pasos para cada categoría
            foreach ($categorias as &$categoria) {
                $sqlPasos = "SELECT id_paso, descripcion_paso, orden FROM pasos_denuncia WHERE id_categoria = ? ORDER BY orden ASC";
                $stmtPasos = $this->conexion->prepare($sqlPasos);
                $stmtPasos->execute([$categoria['id']]);
                $categoria['pasos'] = $stmtPasos->fetchAll(PDO::FETCH_ASSOC);
            }

            return $categorias;
        } catch (PDOException $e) {
            return [];
        }
    }
}
