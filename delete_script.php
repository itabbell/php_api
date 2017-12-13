<?php

$input = fopen("php://stdin", "r");
$keyword = '';
$db = new mysqli('localhost:3306', 'root', 'root', 'php_api_project');

while (true) {
    echo "Enter a keyword, or enter" . " quit " . "to exit:\n";
    echo "php $> ";
    $keyword=trim(fgets($input));
    if ($keyword != 'quit')
    {
        softDeleteByKeyword($keyword, $db);
        echo "\n";
        continue;
    }
    echo "Quitting...\n";
    break;
}


function softDeleteByKeyword($keyword, $db)
{
    // select all entries that match keyword search
    // $sql = "SELECT * FROM `TestProducts` WHERE `name` LIKE ? OR `description` LIKE ? OR `tags` LIKE ?";
    // $query = $db->prepare($sql);
    // $query->bind_param('sss', $keyword, $keyword, $keyword);
    // $query->execute();

    echo "Searching for entries that match " . $keyword . "...\n";
    $results = [];
    $searchTerm = '%' . $keyword . '%';

    // $sql = "SELECT `id`,`name`,`tags`,`description`,`price` FROM `TestProducts` WHERE `name` LIKE ? OR `description` LIKE ? OR `tags` LIKE ? AND `isDeleted` = 0";
    $sql = "
        SELECT 
            sub.`id`, 
            sub.`name`, 
            sub.`tags`,
            sub.`description`,
            sub.`price`
        FROM (
            SELECT *
            FROM `TestProducts` 
            WHERE `name` LIKE ? 
            OR `description` LIKE ? 
            OR `tags` LIKE ?) sub
        WHERE sub.`isDeleted` = 0";

    if ($query = $db->prepare($sql)) {
        $query->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
        $query->execute();
        $query->bind_result($id, $name, $tags, $desc, $price);
    }
    while ($query->fetch()) {
        array_push($results, [
            $id,
            $name,
            $tags,
            $desc,
            $price
        ]);
    }
    echo "Search complete, " . count($results) . " results found.\n";
    echo "Results:\n\n";
    foreach ($results as $result)
    {
        echo "ID: " . $result[0] . "\n";
        echo "\tName: " . $result[1] . "\n";
        echo "\tTags: " . $result[2] . "\n";
        echo "\tDescription: " . $result[3] . "\n";
        echo "\tPrice: " . $result[4] . "\n";
    }
    echo "\n";
    echo "Are you sure you want to delete these items?\n";
    echo "php $> ";
    // have user confirm that selection is acceptable to delete
    $input = fopen("php://stdin", "r");
    $confirmation = trim(fgets($input));

    if (strtolower($confirmation) === "yes")
    {
        echo "Deleting...\n";
        $deletedIDs = "";
        for ($i = 0; $i < count($results) - 1; $i++)
        {
            $deletedIDs .= $results[$i][0] . ",";
        }
        $deletedIDs .= $results[count($results) - 1][0];
        $sql = "
            UPDATE `TestProducts` 
            SET `isDeleted` = 1 
            WHERE `id` IN (" . $deletedIDs . ")";
        // $sql = "
        //     SELECT *
        //     FROM `TestProducts`
        //     WHERE `id`
        //     IN (" . $deletedIDs . ");";
        $db->query($sql);
        echo "Done. Items deleted.\n";
    }
    else
    {
        echo "Cancelling...\n";
    }
    // delete selection
}




// function softDeleteByPhrase($phrase)
// {
//     $sql = "UPDATE `TestProducts` SET isDeleted = 1 WHERE id = ?";
//     $query = $this->dbconn->prepare($sql);
//     $query->bind_param('i', $requestedID);
//     $query->execute();
//     return [ "result" => "success" ];
// }

// SELECT * FROM `TestProducts` WHERE `name` LIKE ? OR `description` LIKE ? OR `tags` LIKE ?;
// UPDATE `TestProducts` SET `isDeleted` = 1 WHERE `name` LIKE ? OR `description` LIKE ? OR `tags` LIKE ?;

