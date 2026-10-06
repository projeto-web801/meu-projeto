<?php
session_start();

include("config/conexao.php");

// Somente usuários podem reservar
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != "usuario") {
    header("Location: login.php");
    exit;
}

// Verifica se recebeu o ID do quarto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: quartos.php");
    exit;
}

$id = intval($_GET['id']);

// Busca o quarto
$stmt = $conexao->prepare("SELECT * FROM quartos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Quarto não encontrado.");
}

$quarto = $resultado->fetch_assoc();


// ======================================================
// CORREÇÃO DA IMAGEM
// ======================================================

$imagemBanco = trim($quarto['imagem'] ?? '');

if ($imagemBanco == '') {

    // Sem imagem cadastrada
    $imagem = "assets/img/quarto1.jpg";

} elseif (
    strpos($imagemBanco, "assets/") === 0 ||
    strpos($imagemBanco, "/assets/") === 0
) {

    // O banco já possui o caminho completo
    $imagem = ltrim($imagemBanco, "/");

} elseif (strpos($imagemBanco, "uploads/") === 0) {

    // Imagem salva na pasta uploads
    $imagem = $imagemBanco;

} else {

    // Banco possui somente o nome do arquivo
    // Primeiro tenta encontrar em assets/
    if (file_exists(__DIR__ . "/assets/" . $imagemBanco)) {

        $imagem = "assets/" . $imagemBanco;

    } elseif (file_exists(__DIR__ . "/assets/img/" . $imagemBanco)) {

        $imagem = "assets/img/" . $imagemBanco;

    } else {

        // Imagem padrão
        $imagem = "assets/img/quarto1.jpg";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Reservar Quarto <?php echo htmlspecialchars($quarto['numero']); ?></title>

<link rel="stylesheet" href="assets/css/style.css">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
rel="stylesheet">

<style>

body {
    background: #f5f5f5;
}

.hotel-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,.12);
    overflow: hidden;
}

.room-img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    display: block;
}

.card-body {
    padding: 25px;
}

.preco {
    font-size: 30px;
    font-weight: bold;
    color: #198754;
}

label {
    font-weight: 600;
    margin-bottom: 6px;
}

</style>

</head>

<body class="dashboard-bg">

<div
class="container py-5"
style="padding-top:110px!important"
>

<div class="row g-4">

<!-- ==================================================
     INFORMAÇÕES DO QUARTO
================================================== -->

<div class="col-md-5">

<div class="hotel-card">

<img
src="<?php echo htmlspecialchars($imagem); ?>"
class="room-img"
alt="Quarto <?php echo htmlspecialchars($quarto['numero']); ?>"
onerror="this.onerror=null;this.src='assets/img/quarto1.jpg';"
>

<div class="card-body">

<h3>
Quarto <?php echo htmlspecialchars($quarto['numero']); ?>
</h3>

<?php if (!empty($quarto['tipo'])): ?>

<p>
<strong>Tipo:</strong>
<?php echo htmlspecialchars($quarto['tipo']); ?>
</p>

<?php endif; ?>

<p>

<?php echo nl2br(
    htmlspecialchars($quarto['descricao'] ?? 'Quarto confortável e completo.')
); ?>

</p>

<p class="preco">

R$
<?php
echo number_format(
    (float)$quarto['preco'],
    2,
    ",",
    "."
);
?>

</p>

<hr>

<p>
👥 <strong>Capacidade:</strong>
<?php echo htmlspecialchars($quarto['capacidade'] ?? 'Não informado'); ?>
hóspedes
</p>

<p>
📶 <strong>Wi-Fi:</strong>
<?php echo htmlspecialchars($quarto['wifi'] ?? 'Não informado'); ?>
</p>

<p>
❄️ <strong>Ar-condicionado:</strong>
<?php echo htmlspecialchars($quarto['ar_condicionado'] ?? 'Não informado'); ?>
</p>

<p>
📺 <strong>TV:</strong>
<?php echo htmlspecialchars($quarto['tv'] ?? 'Não informado'); ?>
</p>

<p>
🥐 <strong>Café da manhã:</strong>
<?php echo htmlspecialchars($quarto['cafe'] ?? 'Não informado'); ?>
</p>

</div>

</div>

</div>


<!-- ==================================================
     FORMULÁRIO DE RESERVA
================================================== -->

<div class="col-md-7">

<div class="hotel-card">

<div class="card-body">

<h2 class="mb-1">
Confirmar reserva
</h2>

<p class="text-secondary">
Informe as datas e os dados da sua estadia.
</p>

<form
action="salvar_reserva.php"
method="POST"
>

<!-- ID DO QUARTO -->

<input
type="hidden"
name="quarto_id"
value="<?php echo (int)$quarto['id']; ?>"
>


<!-- VALOR DA DIÁRIA -->

<input
type="hidden"
name="valor_diaria"
id="valor_diaria"
value="<?php echo htmlspecialchars($quarto['preco']); ?>"
>


<!-- DATA DE ENTRADA -->

<div class="mb-3">

<label for="entrada">
Data de Entrada
</label>

<input
type="date"
name="entrada"
id="entrada"
class="form-control"
required
>

</div>


<!-- DATA DE SAÍDA -->

<div class="mb-3">

<label for="saida">
Data de Saída
</label>

<input
type="date"
name="saida"
id="saida"
class="form-control"
required
>

</div>


<!-- QUANTIDADE DE HÓSPEDES -->

<div class="mb-3">

<label for="hospedes">
Quantidade de Hóspedes
</label>

<input
type="number"
name="hospedes"
id="hospedes"
class="form-control"
min="1"
max="<?php echo (int)($quarto['capacidade'] ?? 1); ?>"
value="1"
required
>

</div>


<!-- PAGAMENTO -->

<div class="mb-3">

<label for="pagamento">
Forma de Pagamento
</label>

<select
name="pagamento"
id="pagamento"
class="form-select"
required
>

<option value="">
Selecione
</option>

<option value="Pix">
Pix
</option>

<option value="Cartão de Crédito">
Cartão de Crédito
</option>

<option value="Cartão de Débito">
Cartão de Débito
</option>

<option value="Dinheiro">
Dinheiro
</option>

</select>

</div>


<!-- VALOR DIÁRIA -->

<div class="mb-3">

<label>
Valor da Diária
</label>

<input
type="text"
class="form-control"
value="R$ <?php echo number_format(
    (float)$quarto['preco'],
    2,
    ',',
    '.'
); ?>"
readonly
>

</div>


<!-- TOTAL -->

<div class="mb-3">

<label>
Total da Reserva
</label>

<input
type="text"
id="total"
class="form-control"
value="R$ 0,00"
readonly
>

</div>


<!-- BOTÕES -->

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success btn-lg"
>

<i class="bi bi-check-circle"></i>

Confirmar Reserva

</button>

<a
href="quartos.php"
class="btn btn-secondary"
>

Cancelar

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>


<script>

// ======================================================
// DATAS
// ======================================================

const hoje = new Date();

const ano = hoje.getFullYear();

const mes = String(hoje.getMonth() + 1).padStart(2, '0');

const dia = String(hoje.getDate()).padStart(2, '0');

const dataHoje = `${ano}-${mes}-${dia}`;

const entrada = document.getElementById("entrada");

const saida = document.getElementById("saida");

const total = document.getElementById("total");


// Impede datas anteriores a hoje

entrada.min = dataHoje;

saida.min = dataHoje;


// ======================================================
// VALOR DA DIÁRIA
// ======================================================

const diaria =
    parseFloat(
        "<?php echo (float)$quarto['preco']; ?>"
    );


// ======================================================
// CALCULAR TOTAL
// ======================================================

function calcularTotal() {

    if (!entrada.value || !saida.value) {

        total.value = "R$ 0,00";

        return;
    }

    const dataEntrada =
        new Date(entrada.value + "T00:00:00");

    const dataSaida =
        new Date(saida.value + "T00:00:00");


    const diferenca =
        dataSaida - dataEntrada;


    const dias =
        diferenca / (1000 * 60 * 60 * 24);


    if (dias > 0) {

        const valor =
            dias * diaria;


        total.value =
            "R$ " +
            valor.toLocaleString(
                "pt-BR",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    } else {

        total.value = "R$ 0,00";
    }
}


// ======================================================
// GARANTE QUE A SAÍDA SEJA DEPOIS DA ENTRADA
// ======================================================

entrada.addEventListener("change", function () {

    if (entrada.value) {

        const dataEntrada =
            new Date(entrada.value + "T00:00:00");

        dataEntrada.setDate(
            dataEntrada.getDate() + 1
        );

        const ano =
            dataEntrada.getFullYear();

        const mes =
            String(
                dataEntrada.getMonth() + 1
            ).padStart(2, "0");

        const dia =
            String(
                dataEntrada.getDate()
            ).padStart(2, "0");

        saida.min =
            `${ano}-${mes}-${dia}`;

        // Se a saída atual for inválida
        if (
            saida.value &&
            saida.value <= entrada.value
        ) {

            saida.value = "";

        }

    } else {

        saida.min = dataHoje;

    }

    calcularTotal();

});


saida.addEventListener(
    "change",
    calcularTotal
);

</script>

<button id="backToTop" class="back-to-top" aria-label="Voltar ao topo"><i class="bi bi-arrow-up"></i></button><script src="assets/js/site.js"></script></body>

</html>