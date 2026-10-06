<?php
session_start();
include("config/conexao.php");

$erro = "";

if(isset($_POST['entrar'])){

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // ==========================
    // LOGIN ADMINISTRADOR
    // ==========================

    $sql = $conexao->prepare("SELECT * FROM administradores WHERE email=?");
    $sql->bind_param("s",$email);
    $sql->execute();

    $resultado = $sql->get_result();

    if($resultado->num_rows > 0){

        $admin = $resultado->fetch_assoc();

        if(password_verify($senha,$admin['senha'])){

            $_SESSION["id"]=$admin["id"]; $_SESSION["nome"]=$admin["nome"]; $_SESSION["admin_id"]=$admin["id"]; $_SESSION["admin_nome"]=$admin["nome"]; $_SESSION["tipo"]="admin";

            header("Location: admin/dashboard.php");
            exit;

        }

    }

    // ==========================
    // LOGIN USUÁRIO
    // ==========================

    $sql = $conexao->prepare("SELECT * FROM usuarios WHERE email=?");
    $sql->bind_param("s",$email);
    $sql->execute();

    $resultado = $sql->get_result();

    if($resultado->num_rows > 0){

        $usuario = $resultado->fetch_assoc();

        if(password_verify($senha,$usuario['senha'])){

            $_SESSION['id']=$usuario['id'];
            $_SESSION['nome']=$usuario['nome'];
            $_SESSION['tipo']="usuario";

            header("Location: usuario/dashboard.php");
            exit;

        }

    }

    $erro = "E-mail ou senha inválidos.";

}
?>

<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Entrar | Hotel Online</title><link rel="stylesheet" href="assets/css/style.css"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></head>
<body class="auth-bg"><div class="container"><div class="row justify-content-center"><div class="col-lg-5 col-md-7">
<div class="auth-card bg-white"><div class="p-4 p-md-5"><div class="text-center mb-4"><div class="fs-1">🏨</div><div class="brand">Hotel Online</div><p class="text-secondary mb-0">Acesse sua conta para continuar</p></div>
<?php if($erro): ?><div class="alert alert-danger rounded-3"><i class="bi bi-exclamation-circle me-2"></i><?=htmlspecialchars($erro)?></div><?php endif; ?>
<form method="POST"><div class="mb-3"><label class="form-label fw-semibold">E-mail</label><input type="email" name="email" class="form-control" placeholder="voce@email.com" required autofocus></div>
<div class="mb-4"><label class="form-label fw-semibold">Senha</label><input type="password" name="senha" class="form-control" placeholder="Sua senha" required></div>
<button type="submit" name="entrar" class="btn btn-gold w-100 py-3"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar</button></form>
<div class="text-center mt-4"><span class="text-secondary">Ainda não possui conta?</span><br><a href="cadastro.php" class="fw-bold text-primary">Criar minha conta</a></div>
<div class="text-center mt-3"><a href="index.php" class="text-secondary small"><i class="bi bi-arrow-left"></i> Voltar ao site</a></div>
</div></div><p class="text-center text-white mt-3 small">© <?=date("Y")?> Hotel Online</p></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><button id="backToTop" class="back-to-top" aria-label="Voltar ao topo"><i class="bi bi-arrow-up"></i></button><script src="assets/js/site.js"></script></body></html>