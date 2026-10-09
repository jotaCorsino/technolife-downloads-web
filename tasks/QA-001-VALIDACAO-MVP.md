# QA-001 — Validação funcional, técnica e de segurança do MVP

**Estado:** CONCLUIDA — 🟢 homologada em 09/10/2026, relatório em [`docs/06-RELATORIO-QA.md`](../docs/06-RELATORIO-QA.md).
**Pré-requisito:** UI-001 homologada pelo responsável e integrada à `main` no PR #2; SCAN-001 homologada no PR #1.
**Agente executor:** Codex, na working copy local `technolife-downloads-web`.
**Branch executada:** `test/QA-001-validacao-mvp`.
**Pull Request:** [#3 — QA-001](https://github.com/jotaCorsino/technolife-downloads-web/pull/3) — integrado à `main`.

## Objetivo

Validar o comportamento real do **catálogo somente leitura** já implementado, consolidar testes reprodutíveis e identificar riscos que precisam estar resolvidos **antes** de publicação na hospedagem. Este trabalho é **QA**, não uma nova versão funcional nem a implantação.

## Procedimento

1. Inspecionar `git status`; sincronizar `main` com `origin/main` após o merge do PR #2, sem destruir mudanças locais.
2. Ler README, `docs/01-ESCOPO.md`, `docs/02-ARQUITETURA.md`, `docs/03-ROADMAP.md`, `docs/05-DECISOES-E-RISCOS.md`, `src/DownloadScanner.php`, `public/` e testes existentes.
3. Criar `test/QA-001-validacao-mvp` a partir da `main` atualizada.
4. Executar os testes existentes e realizar verificações complementares abaixo **somente com fixtures locais fictícias**.
5. Corrigir **apenas defeitos comprovados** dentro do escopo existente, com testes de regressão e justificativa. Não acrescentar funcionalidades por conveniência.
6. Registrar relatório de qualidade, riscos, evidências, comandos e pendências reais em `docs/06-RELATORIO-QA.md` ou documento equivalente, mantendo o material público sanitizado.
7. Atualizar estados no README, roadmap e tarefa em conjunto. Abrir PR de QA e parar em `AGUARDANDO_HOMOLOGACAO`; **não fazer merge nem deploy**.

## Matriz mínima de validação

| Área | Cenários exigidos |
| --- | --- |
| Scanner | Lista somente arquivos regulares do nível imediato; ignora `.htaccess`, dotfiles, subpastas e symlinks; ordenação determinística |
| Nomes e URLs | UTF-8, espaços, acentos, `#`, `?`, caracteres HTML, extensões e arquivos sem extensão; URL HTTPS absoluta, `rawurlencode` apenas do nome original |
| Atualização | Arquivo adicionado/removido na fixture aparece/desaparece após nova requisição, sem armazenamento próprio |
| Estados vazios e falhas | Diretório vazio, inexistente e ilegível; configuração ausente/vazia; base HTTPS inválida; erro genérico sem divulgar caminhos internos |
| Segurança da página | Escape HTML em título, nome, `data-*`, `href` e `aria-label`; ausência de leitura de caminhos via query/header, métodos de escrita, upload, credenciais ou persistência |
| Pesquisa | Filtra imediatamente por nome/título; ignora acentos e caixa; contagem correta; estado sem resultados; retorno ao resultado completo ao limpar |
| Cópia e navegação | Copia URL **exata** com sucesso informado somente após confirmação; falha de permissão/API informa erro; botão abrir utiliza URL direta |
| UX/acessibilidade | Teste visual desktop e 360 px; sem rolagem horizontal; ícones de ação com nomes acessíveis, foco por teclado, legibilidade e estados de tela |
| Ambiente e publicação | Confirmar requisitos de PHP 8.2 local e como configurar `TECHNOLIFE_DOWNLOADS_DIR` e `TECHNOLIFE_DOWNLOADS_BASE_URL` com segurança no cPanel; assegurar que apenas `public/` seria exposto como webroot |

## Testes obrigatórios

- `php -l src/DownloadScanner.php`
- `php -l public/index.php`
- `php -l tests/DownloadScannerTest.php`
- `php -l tests/InterfaceTest.php`
- `php tests/DownloadScannerTest.php`
- `php tests/InterfaceTest.php`
- `node --check public/app.js`
- `node tests/InterfaceScriptTest.js`
- Inspeção de `git diff --check`, alterações e dados sensíveis.
- Rodar servidor PHP local com dados falsos e verificar HTML, pesquisa e cópia no navegador, documentando se alguma verificação não for reproduzível.

## Gate de implantação — pendências a documentar, NÃO executar agora

**Decisão explícita do responsável é obrigatória antes do deploy.** No relatório de QA, registrar um checklist de preparação para DEP-001 com:

1. Onde a aplicação será publicada e qual diretório servirá de **document root**; o código de `src/` e os testes não devem ser servidos pelo navegador.
2. Como o PHP da hospedagem receberá o caminho real de `/downloads/` e a base HTTPS **sem expor caminhos privados ao público ou versioná-los**. Não presumir que variáveis configuradas no terminal chegarão ao PHP-FPM/LiteSpeed.
3. Confirmação de versão PHP compatível, permissões de leitura e link HTTPS real (validação pendente até acesso autorizado ao servidor).
4. **Risco central:** sem login, a página será um **índice publicamente acessível de nomes e links**; verificar que **todos os arquivos listados, inclusive novos uploads futuros**, podem ser divulgados. Uma pasta com material privado impede o deploy nesse formato.
5. Procedimento de rollback e de smoke test para DEP-001, sem usar caminhos ou URLs sensíveis no Git público.

Não apresentar qualquer item de ambiente **não testado** como validado. O relatório deve separar `VALIDADO LOCALMENTE`, `PENDENTE NO CPANEL` e `BLOQUEADOR DE DEPLOY` quando pertinente.

## Critérios de aceite

- [x] Todos os comandos de teste executados com resultados e versões de PHP/Node reportados.
- [x] Cenários negativos e nomes especiais demonstrados sem divulgação de arquivos reais.
- [x] Interface validada em desktop e largura móvel; falhas de cópia e pesquisa testadas.
- [x] Nenhum endpoint de escrita, autenticação, banco, upload ou dependências externas foi introduzido.
- [x] `src/` e `tests/` permaneceriam fora do document root no desenho de implantação proposto.
- [x] Pendências de cPanel e publicabilidade **expressamente documentadas**, sem afirmações falsas de prontidão para produção.
- [x] Nenhum defeito comprovado; nenhuma correção ou teste de regressão novo foi necessário.
- [x] Relatório de QA, PR, branch, commit SHA e evidências preparados para homologação humana.

## Fora do escopo

Deploy, DNS, cPanel de produção, arquivos reais, inspeção do inventário de clientes, publicação do catálogo, novas funcionalidades, métricas, integração HESK e trabalho da DEP-001.

**Gate homologado:** 🟢 QA-001 aprovada pelo responsável em 09/10/2026; merge `cf992aad4f2dc43fdc18c3d23e7f6c2afd38bf61`. Próxima tarefa [DEP-001](DEP-001-IMPLANTACAO-CPANEL.md) autorizada para execução controlada. Publicação depende de verificação do cPanel e da publicabilidade do inventário.
