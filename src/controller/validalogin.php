<?php
session_start();

if (!empty($_POST) && (empty($_POST['txt_email']) || empty($_POST['txt_senha']))) {
    header("Location: login.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "admin", "cadastrodealunos");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$usuario = mysqli_real_escape_string($con, $_POST['txt_email']);
$senha = mysqli_real_escape_string($con, $_POST['txt_senha']);

$sql = "SELECT `id`, `nome`, `nivel` FROM `usuarios` WHERE `usuario` = ? AND `senha` = ? AND `ativo` = 1 LIMIT 1";
$stmt = mysqli_prepare($con, $sql);

if ($stmt) {
    $hashed_password = sha1($senha);
    mysqli_stmt_bind_param($stmt, "ss", $usuario, $hashed_password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $id, $nome, $nivel);
        mysqli_stmt_fetch($stmt);

        $_SESSION['UsuarioID'] = $id;
        $_SESSION['UsuarioNome'] = $nome;
        $_SESSION['UsuarioNivel'] = $nivel;

        mysqli_stmt_close($stmt);
        mysqli_close($con);

        if ($_SESSION['UsuarioNivel'] == 2) {
            header("Location: arquivoaluno.php");
            exit;
        } else {
            header("Location: restrito.php");
            exit;
        }
    } else {
        echo "Login inválido!";
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        exit;
    }
} else {
    echo "Erro ao preparar declaração: " . mysqli_error($con);
    mysqli_close($con);
    exit;
}
?>
