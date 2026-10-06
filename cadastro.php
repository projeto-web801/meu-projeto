<?php
session_start();
include("config/conexao.php");

$mensagem = "";

if(isset($_POST['cadastrar'])){

    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];

    if($senha != $confirmar){

        $mensagem = "<div class='alert alert-danger'>As senhas não coincidem.</div>";

    }else{

        $verifica = $conexao->prepare("SELECT id FROM usuarios WHERE email=?");
        $verifica->bind_param("s",$email);
        $verifica->execute();

        if($verifica->get_result()->num_rows > 0){

            $mensagem = "<div class='alert alert-warning'>Este e-mail já está cadastrado.</div>";

        }else{

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = $conexao->prepare("INSERT INTO usuarios(nome,telefone,email,senha)
            VALUES(?,?,?,?)");

            $sql->bind_param("ssss",$nome,$telefone,$email,$senhaHash);

            if($sql->execute()){

                header("Location: login.php");

                exit;

            }else{

                $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar.</div>";

            }

        }

    }

}
?>

<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Criar conta | Hotel Online</title><link rel="stylesheet" href="assets/css/style.css"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="auth-bg"><div class="container py-4"><div class="row justify-content-center"><div class="col-lg-6">
<div class="auth-card bg-white"><div class="p-4 p-md-5"><div class="text-center mb-4"><div class="fs-1">🏨</div><h2 class="mb-1">Criar sua conta</h2><p class="text-secondary">Cadastre-se para fazer reservas.</p></div>
<?php echo $mensagem; ?>
<form method="POST"><div class="mb-3"><label class="form-label fw-semibold">Nome completo</label><input type="text" name="nome" class="form-control" required></div>
<div class="row g-3"><div class="col-md-6"><label class="form-label fw-semibold">Telefone</label><input type="tel" name="telefone" class="form-control" required></div>
<div class="col-md-6"><label class="form-label fw-semibold">E-mail</label><input type="email" name="email" class="form-control" required></div></div>
<div class="row g-3 mt-1"><div class="col-md-6"><label class="form-label fw-semibold">Senha</label><input type="password" name="senha" class="form-control" minlength="6" required></div>
<div class="col-md-6"><label class="form-label fw-semibold">Confirmar senha</label><input type="password" name="confirmar" class="form-control" minlength="6" required></div></div>
<button type="submit" name="cadastrar" class="btn btn-gold w-100 py-3 mt-4">Criar conta</button></form>
<div class="text-center mt-4"><span class="text-secondary">Já possui uma conta?</span> <a href="login.php" class="fw-bold">Entrar</a></div>
</div></div></div></div></div><button id="backToTop" class="back-to-top" aria-label="Voltar ao topo"><i class="bi bi-arrow-up"></i></button><script src="assets/js/site.js"></script></body></html>