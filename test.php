<?php
require_once('ProductDBContext.php');

$dbContext = new ProductDBContext();
$method = $_SERVER["REQUEST_METHOD"];
$id = $_GET["id"];
$body = json_decode(file_get_contents("php://input"), true);
$response;

switch ($method) {
    case 'GET':
        if ($id)
        {
            $response = $dbContext->getProductByID($id);
            break;
        }
        $response = $dbContext->getAllProducts();
        break;

    case 'POST':
        $response = $dbContext->addNewProduct($body);
        break;

    case 'PUT':
        $response = $dbContext->updateProductByID($id, $body);
        break;

    case 'DELETE':
        $response = $dbContext->deleteProductByID($id);
        break;

    default:
        $response = [ "message" => "cmon bro chill" ];
        http_response_code(501);
        break;
}

header('Content-Type: application/JSON');
file_put_contents("php://output", json_encode($response));

// turn post body json into associative array
// $body = file_get_contents("php://input");
// $decoded = json_decode($body, true);
// var_dump($decoded);

// turn data into json and send as response
// $test = json_encode(new Product([
//     "name" => "testname",
//     "price" => 999.99,
//     "id" => 3,
//     "desc" => "alsdjf;alksdfjal;skd",
//     "tags" => "asdf,asdf,asdf,asdf,"
// ]));
// $test = json_encode([
//     "first" => "this is the first value",
//     "second" => "this is the second value",
// ]);
// header('Content-Type: application/JSON');
// file_put_contents("php://output", $test);