<?php
session_start();
require_once __DIR__ . "/config/conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION["id"]) || ($_SESSION["tipo"] ?? "") !== "usuario") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario_id = $_SESSION['id'];
    $quarto_id = intval($_POST['quarto_id']);
    $entrada = $_POST['entrada'];
    $saida = $_POST['saida'];
    $hospedes = intval($_POST['hospedes']);
    $valor_diaria = floatval($_POST['valor_diaria']);
    $forma_pagamento = $_POST['pagamento'];

    if (!$quarto_id || !$entrada || !$saida || $hospedes < 1 || $valor_diaria <= 0 || !$forma_pagamento) {
        die("Dados da reserva inválidos.");
    }

    if ($entrada < date("Y-m-d")) {
        die("A data de entrada não pode ser anterior à data atual.");
    }

    // Verificar se o quarto continua disponível
    $check = $conexao->prepare("SELECT capacidade, preco, disponibilidade, status FROM quartos WHERE id=? FOR UPDATE");
    $check->bind_param("i", $quarto_id);
    $check->execute();
    $quarto = $check->get_result()->fetch_assoc();
    if (!$quarto) die("Quarto não encontrado.");
    if (($quarto["disponibilidade"] ?? "") !== "Disponivel" && ($quarto["status"] ?? "") !== "Disponivel") {
        die("Este quarto não está mais disponível.");
    }
    if ($hospedes > (int)$quarto["capacidade"]) {
        die("A quantidade de hóspedes excede a capacidade do quarto.");
    }

    // Calcular quantidade de dias
    $dias = (strtotime($saida) - strtotime($entrada)) / 86400;

    if ($dias <= 0) {
        die("A data de saída deve ser maior que a data de entrada.");
    }

    $valor_total = $dias * $valor_diaria;

    // Iniciar transação
    $conexao->begin_transaction();

    try {

        // Salvar reserva
        $sql = $conexao->prepare("
            INSERT INTO reservas
            (usuario_id, quarto_id, data_entrada, data_saida, hospedes, valor_diaria, valor_total, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Confirmada')
        ");

        $sql->bind_param(
            "iissidd",
            $usuario_id,
            $quarto_id,
            $entrada,
            $saida,
            $hospedes,
            $valor_diaria,
            $valor_total
        );

        $sql->execute();

        $reserva_id = $conexao->insert_id;

        // Salvar pagamento
        $sql2 = $conexao->prepare("
            INSERT INTO pagamentos
            (reserva_id, forma_pagamento, valor, status)
            VALUES (?, ?, ?, 'Pendente')
        ");

        $sql2->bind_param(
            "isd",
            $reserva_id,
            $forma_pagamento,
            $valor_total
        );

        $sql2->execute();

        // Atualizar quarto
        $sql3 = $conexao->prepare("
            UPDATE quartos
            SET status='Ocupado', disponibilidade='Ocupado'
            WHERE id=?
        ");

        $sql3->bind_param("i", $quarto_id);
        $sql3->execute();

        // Confirmar transação
        $conexao->commit();

        echo "
        <script>
            alert('Reserva realizada com sucesso!');
            window.location='usuario/dashboard.php';
        </script>
        ";

    } catch (Exception $e) {

        $conexao->rollback();

        echo "Erro ao salvar reserva: " . $e->getMessage();

    }

} else {

    header("Location: quartos.php");

}
?>