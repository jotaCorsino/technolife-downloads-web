# 06 — Relatório QA-001: validação do MVP

- **Data:** 09/10/2026
- **Estado:** CONCLUIDA — 🟢 homologada em 09/10/2026; PR #3 integrado à `main`.
- **Base:** `main` sincronizada inicialmente em `dc03585`, após o merge do PR #2; commits documentais posteriores de `origin/main` até `827b3db` incorporados na branch de QA.
- **Branch de QA:** `test/QA-001-validacao-mvp`.
- **Pull Request:** [#3 — QA-001](https://github.com/jotaCorsino/technolife-downloads-web/pull/3).

## Resultado

**VALIDADO LOCALMENTE:** o catálogo somente leitura passou na suíte existente e nas verificações complementares com arquivos fictícios. Nenhum defeito funcional ou de segurança foi comprovado; por isso, nenhum código da aplicação foi alterado e não houve teste de regressão novo. A validação local **não libera publicação**.

Ambiente: PHP **8.2.34** (CLI e servidor de desenvolvimento) e Node.js **18.19.1**. O servidor local usou `public/` como document root, `TECHNOLIFE_DOWNLOADS_DIR` apontando para uma fixture temporária e `TECHNOLIFE_DOWNLOADS_BASE_URL=https://example.invalid/downloads`. Não foram usados arquivos ou URLs reais da hospedagem.

## Comandos obrigatórios

| Comando | Resultado |
| --- | --- |
| `php -l src/DownloadScanner.php` | PASS — sem erro de sintaxe |
| `php -l public/index.php` | PASS — sem erro de sintaxe |
| `php -l tests/DownloadScannerTest.php` | PASS — sem erro de sintaxe |
| `php -l tests/InterfaceTest.php` | PASS — sem erro de sintaxe |
| `php tests/DownloadScannerTest.php` | PASS — 9 grupos, 6 arquivos regulares |
| `php tests/InterfaceTest.php` | PASS — estados, listagem, escape, URL e ações acessíveis |
| `node --check public/app.js` | PASS — sem erro de sintaxe |
| `node tests/InterfaceScriptTest.js` | PASS — 2 testes, 0 falhas |
| `git diff --check` | PASS — sem problemas |

## Matriz de validação

| Área | Evidência local | Resultado |
| --- | --- | --- |
| Scanner | Teste existente confirmou nível imediato, dotfiles, `.htaccess`, subpastas, symlinks, UTF-8 inválido, controles e ordenação; os arquivos da fixture não foram modificados. | PASS |
| Nomes e URLs | Testes confirmaram espaços, acentos, `#`, `?`, extensão final, nome sem extensão e `rawurlencode` do nome original. No navegador, `QA-"&<tag>.zip` teve aspas, `&` e `<` escapados no HTML; `href` e `data-copy-url` apontaram para `QA-%22%26%3Ctag%3E.zip`. | PASS |
| Atualização | `Novo_Arquivo_QA.txt` apareceu após recarregar (5 → 6 itens) e desapareceu após remoção e nova requisição (6 → 5). | PASS |
| Vazio e falhas | Diretório vazio exibiu `0 arquivos disponíveis`; configuração ausente ou vazia, diretório inexistente/ilegível e base HTTP/inválida produziram estado genérico sem caminho físico. A leitura negada foi reproduzida pelo teste automatizado local. | PASS |
| Segurança da página | Nome semelhante a HTML permaneceu texto, sem elemento injetado. `data-search`, `aria-label`, `href` e URL de cópia foram inspecionados. `?dir=/etc&file=../../etc/passwd` não alterou a lista; `Host: attacker.invalid` não alterou a base HTTPS. Revisão de `public/` e `src/` não encontrou leitura de `$_GET`/`$_POST`/`$_FILES`, escrita, upload, credenciais ou persistência. POST retornou HTTP 200, sem rotina de gravação no código. | PASS |
| Pesquisa | No navegador, `OLA` encontrou `Olá Mundo #1?.tar.gz` e mostrou `1 de 5 arquivos`; busca inexistente mostrou `0 de 5 arquivos` e estado sem resultados; limpar restaurou os 5. | PASS |
| Cópia e navegação | Clipboard do navegador recebeu exatamente `https://example.invalid/downloads/Ol%C3%A1%20Mundo%20%231%3F.tar.gz` e só então exibiu `Link copiado.`. Teste JavaScript cobriu falha da API/permissão. Ícone de abrir apontou à URL HTTPS direta; download real depende da hospedagem. | PASS local / download real pendente |
| UX e acesso | Inspeção visual em 1200 px e 360 px; larguras de documento 1185 px e 345 px, sem rolagem horizontal ou linhas cortadas. Ações têm `aria-label`, texto visual apenas nos ícones e foco de teclado com contorno visível. Estados vazio e erro foram inspecionados no navegador. | PASS |
| Document root | Com `php -S ... -t public`, pedidos a `/src/DownloadScanner.php` e `/tests/DownloadScannerTest.php` retornaram HTTP 404. | PASS local |

As fixtures foram criadas em diretório temporário com conteúdo fictício. A abertura/descarga do arquivo em um servidor HTTPS real, a permissão de leitura real e a configuração do PHP da hospedagem **não foram testadas**.

## PENDENTE NO CPANEL — preparação para DEP-001

- [ ] Definir domínio/caminho de publicação, inclusive subpasta exclusiva se aplicável, sem alterar HESK ou `/downloads/`. O webroot efetivo deve expor **somente o conteúdo de `public/`**; manter `src/`, `tests/`, `tasks/`, `docs/` e metadados Git fora dele. Confirmar por requisição HTTP que código e testes não são servidos.
- [ ] Confirmar no ambiente real a versão de PHP **8.2 ou superior** e o handler ativo. O [MultiPHP Manager do cPanel](https://docs.cpanel.net/cpanel/software/multiphp-manager-for-cpanel/) documenta a seleção da versão por domínio; a disponibilidade depende do provedor.
- [ ] Configurar `TECHNOLIFE_DOWNLOADS_DIR` com o caminho absoluto real e `TECHNOLIFE_DOWNLOADS_BASE_URL` com a base HTTPS real **fora do repositório e do webroot**. A forma de fornecer variáveis ao processo HTTP deve ser confirmada com o provedor para o handler usado. Variáveis do shell local não comprovam disponibilidade no PHP-FPM ou LiteSpeed; a [documentação de pools PHP-FPM do cPanel](https://docs.cpanel.net/knowledge-base/php-fpm/php-fpm-domain-pools/) descreve configurações por domínio, enquanto o [cPanel ressalta que LiteSpeed não usa sua implementação de PHP-FPM](https://docs.cpanel.net/knowledge-base/php-fpm/php-fastcgi-process-manager-php-fpm/). Validar `getenv()` no contexto da requisição sem publicar valores ou paths.
- [ ] Confirmar que o processo PHP consegue ler a pasta real e que `open_basedir`, se ativo, permite tanto a pasta de downloads quanto `src/`. Verificar a URL HTTPS e a resposta do servidor para nomes especiais, sem versionar dados privados.
- [ ] Preparar rollback para a versão anterior do webroot/configuração e smoke test após implantação: página e assets, listagem autorizada, pesquisa, cópia, URL HTTPS, erros genéricos e HTTP 404 para arquivos de código fora de `public/`. Registrar procedimentos internos sem caminhos/URLs sensíveis neste repositório público.

## BLOQUEADOR DE DEPLOY

**Decisão explícita do responsável sobre publicabilidade:** sem login, o catálogo revela nomes e links de **todos** os arquivos elegíveis da pasta, inclusive novos uploads futuros. Inspecionar a origem real e estabelecer controle operacional que impeça material privado nela. Qualquer arquivo não publicável bloqueia a implantação nesse formato. A publicação também fica bloqueada até que document root, variáveis do PHP HTTP, versão/permissões e URL HTTPS sejam confirmados no cPanel.

**Gate ÓRBITA:** QA-001 homologada pelo responsável humano em 09/10/2026, PR #3 integrado no merge `cf992aad4f2dc43fdc18c3d23e7f6c2afd38bf61`. DEP-001 autorizada para preparação e verificações técnicas. **O deploy público continua condicionado** à decisão explícita sobre publicabilidade, configuração segura e smoke tests no cPanel.
