<?php
class ProductDBContext
{
    public function __construct()
    {
        $this->dbconn = new mysqli('localhost:3306', 'root', 'root', 'php_api_project');
    }

    public function addNewProduct($newProduct)
    {
        $sql = "INSERT INTO `TestProducts` (`name`, `description`, `price`, `tags`) VALUES (?, ?, ?, ?)";
        $query = $this->dbconn->prepare($sql);
        $query->bind_param(
            'ssis', 
            $newProduct["name"], 
            $newProduct["desc"], 
            $newProduct["price"], 
            $newProduct["tags"]
        );
        $query->execute();
        return [ "result" => "success" ];
    }

    public function getAllProducts()
    {
        $assoc_array = [];
        $sql = "SELECT `id`, `name`, `description`, `price`, `tags` FROM `TestProducts` WHERE `isDeleted` = 0";
        $result = $this->dbconn->query($sql);
        while ($row = $result->fetch_assoc()) {
            array_push($assoc_array, $row);
        }
        return $assoc_array;
    }

    public function getProductByID($requestedID)
    {
        $sql = "SELECT `id`, `name`, `description`, `price`, `tags` FROM `TestProducts` WHERE `id` = ? AND `isDeleted` = 0 LIMIT 1";
        $query = $this->dbconn->prepare($sql);
        $query->bind_param('i', $requestedID);
        $query->execute();
        $query->bind_result($id, $name, $desc, $price, $tags);
        $query->fetch();
        return array(
            "id" => $id,
            "name" => $name,
            "desc" => $desc,
            "price" => $price,
            "tags" => $tags
        );
    }

    public function updateProductByID($requestedID, $updates)
    {
        $sql = $query = $bindTypes = $sqlUpdate = "";
        $sqlChunks = $paramValues = [];
        $validFields = array(
            "name" => [
                "name",
                "s"
            ],            
            "desc" => [
                "description",
                "s"
            ],
            "price" => [
                "price",
                "i"
            ],
            "tags" => [
                "tags",
                "s"
            ]
        );

        foreach ($validFields as $key => $queryData)
        {
            $update = $updates[$key];
            if ($update) {
                $sqlChunk = $queryData[0] . " = ?";
                array_push($sqlChunks, $sqlChunk);
                array_push($paramValues, $update);
                $bindTypes .= $queryData[1];
            }
        }
        // for id param
        $bindTypes .= "i";
        array_push($paramValues, $requestedID);

        for ($i = 0; $i < count($sqlChunks); $i++)
        {
            if ($i == count($sqlChunks) - 1)
            {
                $sqlUpdate .= $sqlChunks[$i];
                break;
            }
            $sqlUpdate .= $sqlChunks[$i] . ", ";
        }

        $sql = "UPDATE `TestProducts` SET " . $sqlUpdate . " WHERE id = ?";
        $query = $this->dbconn->prepare($sql);
        $query->bind_param($bindTypes, ...$paramValues);
        $query->execute();
        return [ "result" => "success" ];
    }

    public function deleteProductByID($requestedID)
    {
        $sql = "UPDATE `TestProducts` SET isDeleted = 1 WHERE id = ?";
        $query = $this->dbconn->prepare($sql);
        $query->bind_param('i', $requestedID);
        $query->execute();
        return [ "result" => "success" ];
    }
}