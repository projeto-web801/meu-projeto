HOTEL ONLINE (H.O.) — VERSÃO MELHORADA

Esta versão foi melhorada diretamente sobre a estrutura existente do projeto.

PRINCIPAIS ALTERAÇÕES
- Interface visual modernizada mantendo a identidade azul-marinho + dourado.
- Busca com lupa na navegação e busca de quartos por número/descrição.
- Botão flutuante "voltar ao topo" com rolagem suave.
- Painel administrativo responsivo com menu lateral recolhível no celular.
- Dashboard com indicadores calculados diretamente no banco.
- Gerenciamento de quartos com busca, filtro, status e proteção contra exclusão de quarto ocupado.
- Imagens de quartos aceitam URL e arquivos locais, preservando o valor salvo no banco.
- Reservas com filtros por hóspede, quarto e status.
- Nova tela admin/estadias.php para controle de check-out.
- Hóspedes baseados na tabela existente "usuarios".
- Pagamentos e relatórios reorganizados.
- Correção dos includes administrativos que apontavam para caminhos inexistentes.
- Login administrativo usando password_verify e migração automática de senha em texto puro quando necessário.
- Consultas preparadas nas operações com entrada do usuário.
- Mensagens e botões administrativos mais claros.
- Compatibilidade mantida com o banco "hotel_online" e os nomes de tabelas/colunas já utilizados.

ESTRUTURA
- config/conexao.php — conexão MySQL
- assets/css/style.css — estilos globais
- assets/js/site.js — busca, menu mobile e voltar ao topo
- admin/_header.php e admin/_footer.php — layout compartilhado do painel
- admin/estadias.php — controle das estadias

IMPORTANTE
O projeto depende do banco MySQL/MariaDB existente "hotel_online".
Faça backup do banco antes de testar alterações em ambiente real.
As estatísticas do dashboard não usam números fictícios: são consultas ao banco.
