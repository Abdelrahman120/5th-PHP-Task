<?php
class Database
{
    function connection($db_host, $db_user, $db_pass, $db_name)
    {
        try {
            $dsn = "mysql:host={$db_host};dbname={$db_name};";
            $pdo = new PDO($dsn, $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            return false;
        }
        return $pdo;
    }

    function insert($db, $tableName, $info)
    {
        $columns = "";
        $values = "";
        foreach ($info as $colName => $colValue) {
            $columns .= "`$colName`, ";
            $values .= ":$colName, ";
        }
        $columns = rtrim($columns, ", ");
        $values = rtrim($values, ", ");
        $inst_query = "INSERT INTO `$tableName` ($columns) VALUES ($values)";

        try {
            $inst_stmt = $db->prepare($inst_query);
            foreach ($info as $colName => $colValue) {
                $inst_stmt->bindValue(":$colName", $colValue);
            }
            $inst_stmt->execute();
            if ($db->lastInsertId()) {
                header("Location: Table.php");
                exit;
            }
        } catch (PDOException $e) {
            echo "Error in inserting database {$e->getMessage()}";
        }
    }
    function select($db, $tableName)
    {
        if ($db) {
            try {
                $select_query = "SELECT * FROM $tableName;";
                $stmt = $db->prepare($select_query);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $users;
            } catch (PDOException $e) {
                echo "{$e->getMessage()}";
            }
        }
    }

    function update($db, $tableName, $id, $values)
    {
        $col_Val = "";
        foreach ($values as $colName => $colValue) {
            $col_Val .= "`$colName` = :$colName, ";
        }
        $col_Val = rtrim($col_Val, ", ");
        $update_query = "UPDATE `$tableName` SET $col_Val WHERE `id` = :id";

        try {
            $update_stmt = $db->prepare($update_query);

            foreach ($values as $colName => $colValue) {
                $update_stmt->bindValue(":$colName", $colValue);
            }
            $update_stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $update_stmt->execute();
            header("Location: Table.php");
            exit;
        } catch (PDOException $e) {
            echo "Error in updating database {$e->getMessage()}";
        }
    }


    function delete($db, $tableName, $id)
    {
        if ($id && $db) {
            try {
                $select_query = "SELECT * FROM `$tableName` WHERE id = :id";
                $stmt = $db->prepare($select_query);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                $obj = $stmt->fetch(PDO::FETCH_ASSOC);
                $img_path = $obj["pic"];
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
                $delete_query = "DELETE FROM `$tableName` WHERE id = :id";
                $stmt = $db->prepare($delete_query);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();

                header("Location: Table.php");
                exit;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
}

$obj = new Database;
$db = $obj->connection("localhost", "newuser", "password", "php_banha");