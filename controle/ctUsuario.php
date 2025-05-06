<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Usuarios.php';

$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $usuario->id = $_GET['id'];
            $usuario->readOne();
            
            if($usuario->nome != null) {
                $usuario_arr = array(
                    "id" => $usuario->id,
                    "nome" => $usuario->nome,
                    "email" => $usuario->email,
                    "ra" => $usuario->ra,
                    "celular" => $usuario->celular
                );
                http_response_code(200);
                echo json_encode($usuario_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Usuário não encontrado."));
            }
        } else {
            $stmt = $usuario->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $usuarios_arr = array();
                $usuarios_arr["records"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $usuario_item = array(
                        "id" => $id,
                        "nome" => $nome,
                        "email" => $email,
                        "ra" => $ra,
                        "celular" => $celular
                    );
                    array_push($usuarios_arr["records"], $usuario_item);
                }
                
                http_response_code(200);
                echo json_encode($usuarios_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Nenhum usuário encontrado."));
            }
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->nome) && !empty($data->email) && !empty($data->ra) && !empty($data->senha) && !empty($data->celular)) {
            $usuario->nome = $data->nome;
            $usuario->email = $data->email;
            $usuario->ra = $data->ra;
            $usuario->senha = $data->senha;
            $usuario->celular = $data->celular;
            
            if($usuario->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Usuário criado com sucesso."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o usuário."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos. Não foi possível criar o usuário."));
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        $usuario->id = $data->id;
        $usuario->nome = $data->nome;
        $usuario->email = $data->email;
        $usuario->ra = $data->ra;
        $usuario->celular = $data->celular;
        
        if($usuario->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Usuário atualizado com sucesso."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível atualizar o usuário."));
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        $usuario->id = $data->id;
        
        if($usuario->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Usuário excluído com sucesso."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível excluir o usuário."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>