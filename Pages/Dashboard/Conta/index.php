<?php
try {
    require "../../../Data/Conexao.php";
    $Conta_Id = isset($_GET["Conta_Id"]) ? $_GET["Conta_Id"] : "1";
    $_SESSION["SessaoContaId"] = $Conta_Id;
    $User_Id = $_SESSION["SessaoUserId"];

    $sql = "SELECT Contas.*
        FROM Contas
        INNER JOIN Useres
        ON Contas.User_Id = Useres.User_Id
        WHERE Useres.User_Id = $User_Id
        AND $Conta_Id = Contas.Conta_Id";
    $resultContas = $conn->query($sql);
    $numLinhasContas = $resultContas->num_rows;
    $linha = $resultContas->fetch_assoc();

    $sql = "SELECT Registros.*
        FROM Contas
        INNER JOIN Useres
        ON Contas.User_Id = Useres.User_Id
        INNER JOIN Registros
        ON Contas.Conta_Id = Registros.Conta_Id
        WHERE Useres.User_Id = $User_Id
        AND $Conta_Id = Contas.Conta_Id
        AND Registros.Conta_Id = Contas.Conta_Id";

    $resultRegistros = $conn->query($sql);
} catch (Exception $e) {
    echo "Aconteceu algo de errado!";
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/css/dataTables.bootstrap4.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="../../../style/reset.css">
    <link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">
    <title>Carteira Online</title>
</head>

<body>
    <?php
    if ($User_Id > 0 && $numLinhasContas > 0) { ?>

        <header class="w-100 text-center align-middle">
            <h1><?php echo $linha["Nome"]; ?></h1>
            <p><?php echo $linha["Balanco"]; ?>/<?php echo $linha["Valor"]; ?></p>
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <a class="navbar-brand" href="../../../index.html">Carteira Online</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-item nav-link" href="../../../index.html">Home</a>
                        <a class="nav-item nav-link" href="../index.php">Dashboard</a>
                    </div>
                    <div class="navbar-nav">
                        <!-- <a class="nav-item nav-link" href="./Pages/LogIn_Registro/index.php">Entrar/Registrar</a> -->
                        <a class="nav-item nav-link" href="../../../Data/Registro/Sair.php">Sair</a>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <?php if ($numLinhasContas > 0) { ?>
                <h2>
                    <span>Editar</span>
                    <a href="../../../Data/Contas/Eliminar.php?Conta_Id=<?php echo $Conta_Id; ?>">
                        <button class="btn btn-danger">
                            <i>
                                <img src="../../../Assets/Icons/trash.svg" alt="Eliminar">
                            </i>
                        </button>
                    </a>
                </h2>

                <form class="formEditarConta shadow-lg p-3 mb-5 bg-white rounded" action="../../../Data/Contas/Editar.php" method="GET">
                    <input type="text" placeholder="Nome da Conta" pattern=".{1,30}" name="Nome" value="<?php echo $linha["Nome"]; ?>" required>
                    <input type="number" min="0" placeholder="Valor" name="Valor" value="<?php echo $linha["Valor"]; ?>">
                    <input type="number" min="0" placeholder="Mensalidade" title="Objetivo mensal" name="Mensalidade" value="<?php echo $linha["Mensalidade"]; ?>">
                    <input type="date" name="DataFinal" id="DataFinal" value="<?php echo $linha["DataFinal"]; ?>">
                    <textarea placeholder="Descrição" pattern=".{0,500}" name="Descricao" value="<?php echo $linha["Descricao"]; ?>"><?php echo $linha["Descricao"]; ?></textarea>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>



                <div class="container table-responsive">
                    <h2>Registro</h2>

                    <form class="formRegistros" action="../../../Data/Contas/Registros/Criar.php">
                        <input type="text" placeholder="Nome do Registro" pattern=".{1,30}" name="Nome">
                        <input type="number" placeholder="Montante" name="Montante" value="0" required>
                        <input type="date" name="Data" id="DataRegistro">
                        <button type="submit" class="btn btn-primary">Criar</button>
                    </form>

                    <?php if ($resultRegistros->num_rows > 0) { ?>

                        <table class="table table-bordered table-striped table-hover table-condensed" cellspacing="0" width="100%" id="TabelaContas">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Valor</th>
                                    <th scope="col">Data</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php while ($numLinhaRegistros = $resultRegistros->fetch_assoc()) { ?>
                                    <tr>
                                        <td> <?php echo $numLinhaRegistros["Nome"]; ?></td>
                                        <td> <?php echo $numLinhaRegistros["Montante"]; ?></td>
                                        <td> <?php echo $numLinhaRegistros["Data"]; ?></td>
                                        <td class="TabEliminar">
                                            <a href="../../../Data/Contas/Registros/Eliminar.php?Registro_Id=<?php echo $numLinhaRegistros['Registro_Id']; ?>" class="btn btn-danger">Apagar</a>
                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>

                <?php
                    } else {
                        echo "<h2>Não tens nenhum Registro registrado</h2>";
                    }
                } else {
                    echo "<h2>Não tens acesso a esta conta!! <i>🚷</i></h2>";
                }
                ?>
                </div>
        </main>

    <?php } else {
        echo "<h2>Conta eliminada ou não tens acesso ou Inicia sessão!</h2>";
    }
    ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.13/js/dataTables.bootstrap4.min.js"></script>

    <script src="./script"></script>
</body>

</html>