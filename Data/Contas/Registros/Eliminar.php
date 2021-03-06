<?php require "../../Conexao.php"; ?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css?v=<?php echo time(); ?>">
    <title>Eliminar Registro</title>
</head>

<body>
    <?php
        $User_Id = isset($_SESSION["SessaoUserId"])?$_SESSION["SessaoUserId"]:0;
        $Conta_Id =  isset($_SESSION["SessaoContaId"])?$_SESSION["SessaoContaId"]:0;
        $Registro_Id =  isset($_GET["Registro_Id"])?$_SESSION["Registro_Id"]:0;
        if (isset($User_Id) & isset($Conta_Id) & isset($Registro_Id)) {
            $sql = "SELECT *
                FROM Contas
                INNER JOIN Useres
                ON Contas.User_Id = Useres.User_Id
                WHERE Useres.User_Id = $User_Id
                AND $Conta_Id = Contas.Conta_Id";
            $resultContas = $conn->query($sql);
            if (!(isset($resultContas->num_rows) > 0)) {
                MensFunc("Não tens acesso a esta operação!");
            } else {
                $sql = "SELECT *
                    FROM Registros
                    WHERE $User_Id = User_Id
                    AND $Conta_Id = Conta_Id
                    AND $Registro_Id = Registro_Id";
                $resultRegistro = $conn->query($sql);
                if (!isset($resultRegistro->num_rows) > 0) {
                    MensFunc("Não foi possivel eliminar o registro!");
                } else {
                    $sql = "DELETE FROM Registros WHERE $Registro_Id = Registro_Id;";
                    if ($conn->query($sql) === TRUE) {
                        $sqlSoma = "SELECT SUM(Montante) AS Montante
                        FROM Registros
                        WHERE $Conta_Id = Conta_Id;";
                        $resultSoma = $conn->query($sqlSoma);
                        $resultSoma = $resultSoma->fetch_assoc();
                        $resultSoma = $resultSoma["Montante"];
                        $resultSoma = $resultSoma ? $resultSoma : 0;
                        try {
                            $sqlBalanco = "UPDATE `Contas`
                            SET Balanco = $resultSoma
                            WHERE `Contas`.`Conta_Id` = $Conta_Id;";
                            $conn->query($sqlBalanco);
                        } catch (Exception $e) {
                            MensFunc("Não foi possivel alterar o balanço!");
                        }
                        MensFunc("O Registro foi eliminado!", false);
                        header('Location: ' . "../../../Pages/Dashboard/Conta/index.php?Conta_Id=$Conta_Id");
                    } else {
                        MensFunc("Não foi possivel eliminar o Registro!");
                    }
                }
            }
        }else {
            MensFunc("Dados errados ou insuficientes!");
        }

        function MensFunc($mensagem, $IsErro = true)
        {
            echo "<h2>$mensagem<h2><br>";
            if ($IsErro) { // É um erro
                echo "<p>Porfavor tenta mais tarde ou confirma se escreveste tudo corretamente e tens a sessão iniciada!<p><br>";
            }
        }
    ?>
    <a href="../../../Pages/Dashboard/Conta/index.php?Conta_Id=<?php echo $Conta_Id;?>">Voltar</a>
</body>

</html>