<?php
include("70_conexion.php");

$id = $_GET["id"];

// Preparamos la consulta sql    
$sql = "DELETE FROM datosUsuarios 
        WHERE id = $id";
//echo "<br>Query: $sql";
$stmtPDO = $conexionDB->prepare($sql);

// Ejecutamos la consulta
$stmtPDO->execute();

header("Location: 70_crud_i_index.php");

  /*
  //echo "<br>Query: $sql";
  $stmtPDO = $conexionDB->prepare($sql);

  // Ejecutamos la consulta
  $stmtPDO->execute();

  // registros es un array de objetos
  $registros = $stmtPDO->fetchAll(PDO::FETCH_OBJ);
*/