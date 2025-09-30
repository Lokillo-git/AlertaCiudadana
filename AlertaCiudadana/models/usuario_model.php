<?php require_once __DIR__ . '/../config/database.php';

class UsuarioModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    public function registrarUsuario($nombre, $email, $password, $genero, $direccion, $telefono, $foto_perfil, $latitud, $longitud)
    {
        // Hashear la contraseña antes de insertarla
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Preparar la consulta SQL para insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, email, password, genero, direccion, telefono, foto_perfil, latitud, longitud)
                VALUES (:nombre, :email, :password, :genero, :direccion, :telefono, :foto_perfil, :latitud, :longitud)";

        // Preparar la declaración
        $stmt = $this->conexion->prepare($sql);

        // Vincular los parámetros
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':foto_perfil', $foto_perfil, PDO::PARAM_LOB); // Usar PDO::PARAM_LOB para archivos binarios
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);

        // Ejecutar la consulta y verificar si se insertó el registro correctamente
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function iniciarSesion($email, $password)
    {
        // Incluye foto_perfil en la consulta
        $sql = "SELECT id, nombre, email, password, foto_perfil FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }

        return false;
    }


    /**
     * Obtiene los datos completos de un usuario por su ID (para precargar formulario)
     */
    public function obtenerUsuarioCompleto($id)
    {
        try {
            $sql = "SELECT id, nombre, email, genero, direccion, telefono, foto_perfil, latitud, longitud 
                    FROM usuarios WHERE id = ? LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id]);

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && $usuario['foto_perfil']) {
                // Convertir BLOB a base64 para fácil manejo en formularios
                $usuario['foto_perfil'] = base64_encode($usuario['foto_perfil']);
            }

            return $usuario;
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza los datos de un usuario existente
     */
    public function actualizarUsuario($id, $nombre, $email, $genero, $direccion, $telefono, $foto_perfil = null, $latitud = null, $longitud = null)
    {
        try {
            $fotoBlob = $foto_perfil ? base64_decode($foto_perfil) : null;

            if ($fotoBlob) {
                // Actualizar incluyendo la foto
                $sql = "UPDATE usuarios SET 
                        nombre = ?, 
                        email = ?, 
                        genero = ?, 
                        direccion = ?, 
                        telefono = ?, 
                        foto_perfil = ?, 
                        latitud = ?, 
                        longitud = ? 
                        WHERE id = ?";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(1, $nombre);
                $stmt->bindParam(2, $email);
                $stmt->bindParam(3, $genero);
                $stmt->bindParam(4, $direccion);
                $stmt->bindParam(5, $telefono);
                $stmt->bindParam(6, $fotoBlob, PDO::PARAM_LOB);
                $stmt->bindParam(7, $latitud);
                $stmt->bindParam(8, $longitud);
                $stmt->bindParam(9, $id, PDO::PARAM_INT);
            } else {
                // Actualizar sin cambiar la foto
                $sql = "UPDATE usuarios SET 
                        nombre = ?, 
                        email = ?, 
                        genero = ?, 
                        direccion = ?, 
                        telefono = ?, 
                        latitud = ?, 
                        longitud = ? 
                        WHERE id = ?";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(1, $nombre);
                $stmt->bindParam(2, $email);
                $stmt->bindParam(3, $genero);
                $stmt->bindParam(4, $direccion);
                $stmt->bindParam(5, $telefono);
                $stmt->bindParam(6, $latitud);
                $stmt->bindParam(7, $longitud);
                $stmt->bindParam(8, $id, PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un email existe (excepto para el usuario actual)
     */
    public function existeEmail($email, $excluirId = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
            $params = [$email];

            if ($excluirId) {
                $sql .= " AND id != ?";
                $params[] = $excluirId;
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar email: " . $e->getMessage());
            return false;
        }
    }

    // Función para obtener las denuncias asociadas al usuario
    public function obtenerDenunciasPorUsuario($usuarioId)
    {
        try {
            // Obtenemos las denuncias del usuario con la información del agente y categoría
            $queryDenuncias = "
            SELECT 
                d.id AS denuncia_id,
                d.titulo,
                d.estado,
                d.fecha_creacion,
                c.nombre AS categoria_nombre,
                a.nombre AS agente_nombre
            FROM 
                denuncias d
            LEFT JOIN 
                categorias c ON d.categoria_id = c.id
            LEFT JOIN 
                agentes a ON d.agente_id = a.id
            WHERE 
                d.usuario_id = :usuario_id
            ORDER BY 
                d.fecha_creacion DESC
            ";

            $stmtDenuncias = $this->conexion->prepare($queryDenuncias);
            $stmtDenuncias->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmtDenuncias->execute();

            $denuncias = $stmtDenuncias->fetchAll(PDO::FETCH_ASSOC);

            return $denuncias;
        } catch (PDOException $e) {
            error_log("Error en obtenerDenunciasPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    // Función para obtener la denuncia con su información asociada
    public function obtenerDetallesDenuncia($denunciaId)
    {
        try {
            // Obtener la denuncia específica con los datos básicos
            $queryDenuncia = "
                SELECT 
                    d.id,
                    d.titulo,
                    d.descripcion,
                    d.estado,
                    d.fecha_creacion,
                    d.ubicacion,
                    c.nombre AS categoria_nombre,
                    a.nombre AS agente_nombre
                FROM 
                    denuncias d
                LEFT JOIN 
                    categorias c ON d.categoria_id = c.id
                LEFT JOIN 
                    agentes a ON d.agente_id = a.id
                WHERE 
                    d.id = :denuncia_id
            ";

            $stmtDenuncia = $this->conexion->prepare($queryDenuncia);
            $stmtDenuncia->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmtDenuncia->execute();

            $denuncia = $stmtDenuncia->fetch(PDO::FETCH_ASSOC);

            if (!$denuncia) {
                return ['error' => 'Denuncia no encontrada'];
            }

            // Obtener los pasos del protocolo de esta denuncia
            $queryPasos = "
                SELECT 
                    pd.id_paso, 
                    pd.descripcion_paso, 
                    sd.completado, 
                    sd.fecha_completado 
                FROM 
                    pasos_denuncia pd
                LEFT JOIN 
                    seguimiento_denuncia sd ON pd.id_paso = sd.id_paso 
                    AND sd.id_denuncia = :denuncia_id
                WHERE 
                    pd.id_categoria = (SELECT categoria_id FROM denuncias WHERE id = :denuncia_id)
                ORDER BY 
                    pd.orden
            ";

            $stmtPasos = $this->conexion->prepare($queryPasos);
            $stmtPasos->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmtPasos->execute();

            $pasos = $stmtPasos->fetchAll(PDO::FETCH_ASSOC);

            // Obtener las evidencias para cada paso
            foreach ($pasos as &$paso) {
                if ($paso['completado']) {
                    $queryEvidencias = "
            SELECT archivo_path, fecha_subida, Descripcion
            FROM evidencias_seguimiento 
            WHERE id_seguimiento = (SELECT id_seguimiento FROM seguimiento_denuncia WHERE id_paso = :id_paso AND id_denuncia = :denuncia_id)
        ";

                    $stmtEvidencias = $this->conexion->prepare($queryEvidencias);
                    $stmtEvidencias->bindParam(':id_paso', $paso['id_paso'], PDO::PARAM_INT);
                    $stmtEvidencias->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
                    $stmtEvidencias->execute();

                    $evidencias = $stmtEvidencias->fetchAll(PDO::FETCH_ASSOC);

                    // Asignar las evidencias recuperadas al paso correspondiente
                    $paso['evidencias'] = $evidencias;
                }
            }

            return [
                'denuncia' => $denuncia,
                'pasos' => $pasos
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerDetallesDenuncia: " . $e->getMessage());
            return ['error' => 'Error al obtener los detalles de la denuncia'];
        }
    }
}
