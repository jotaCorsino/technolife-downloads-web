# DEP-001 — Implantação controlada da Central de Links

**Estado:** FASE_A_AGUARDANDO_REVISAO em 09/10/2026 — 🔵 [pacote e instruções locais](../docs/07-PREPARACAO-DEP-001.md) preparados; implantação manual no cPanel ainda pendente e condicionada aos gates de segurança.
**Prazo operacional solicitado:** 09/10/2026, **até 15h (America/Sao_Paulo)**.
**Objetivo:** disponibilizar a Central de Links para uso dos técnicos da Technolife na hospedagem existente.
**Destino definido pelo responsável (09/10/2026):** `https://suporte.technolife.net.br/links/` (também acessível digitando `https://suporte.technolife.net.br/links`, mediante resolução ou redirecionamento normal do servidor).
**Pasta:** criar a nova subpasta pública `links` na raiz web existente do domínio `suporte.technolife.net.br`; **não** colocar a página dentro de `/downloads/`. A localização física precisa ser confirmada no cPanel.
**Pré-requisitos:** QA-001 homologada e integrada à `main` no PR #3. O responsável autorizou **iniciar a DEP-001**. A publicação pública exige **confirmação separada da publicabilidade de todos os arquivos atuais e futuros**.

## Separação obrigatória dos ambientes

**O Codex executa no notebook de desenvolvimento, não no servidor cPanel.** Não presumir que ele disponha de SSH, cPanel, File Manager ou de acesso aos arquivos reais da empresa. A autorização de início da DEP-001 **não** concede acesso remoto nem valida o ambiente de produção.

### Fase A — Preparação local com Codex (executar agora)

1. Sincronizar `main`, criar uma branch própria `deploy/DEP-001-preparacao-links` e ler esta tarefa e o relatório de QA.
2. Preparar **um pacote implantável local**, destinado especificamente à URL `https://suporte.technolife.net.br/links/` e à subpasta pública `links/`.
3. O pacote deve distinguir claramente os **arquivos a enviar à pasta pública `links/`** (somente `index.php`, CSS, JS e `assets/`) do **leitor PHP privado e da configuração privada**, destinados a um local fora de toda a raiz pública do domínio. **Jamais** copiar `src/` para a raiz pública por causa do `../src/DownloadScanner.php` atual.
4. Adequar de forma mínima e segura o bootstrap/require do scanner para funcionar com a separação física entre `links/` e o código privado. O código versionado não pode conter caminhos reais, senhas ou segredos. Se a configuração por `getenv` não estiver disponível no servidor, preparar alternativa por arquivo **privado** de configuração, com exemplo fictício versionável; valores reais serão preenchidos somente no cPanel. Validar localmente o include e o comportamento fail-closed sem configuração.
5. Testar em diretórios fictícios que reproduzam a separação **webroot da pasta `links/` x arquivos privados externos**; preservar as suítes SCAN-001 e UI-001. Verificar também HTTP 404 para arquivos que não podem ser acessados pela web.
6. Produzir instruções objetivas para implantação **manual pelo cPanel File Manager**: pastas a criar, conteúdo exato a enviar a cada pasta, configuração no contexto PHP HTTP, verificações, rollback. Nunca registrar os caminhos físicos reais do servidor no repositório público.
7. Registrar commits, testes e um PR apenas da preparação; interromper antes de qualquer ação remota ou publicação.

### Fase B — Implantação no cPanel (realizada pelo responsável, com orientação)

1. O responsável acessa o cPanel do servidor da empresa, confirma a raiz web do domínio e cria a subpasta **`links/`** nessa raiz.
2. Após aprovação do pacote, envia cada conjunto de arquivos ao destino correto **usando o File Manager**, sem colocar código privado, `.git/`, `docs/` ou `tests/` dentro da webroot.
3. Configura os valores reais de diretório e URL-base somente no servidor, de forma privada, após verificar a versão/handler PHP e as permissões.
4. **Antes de tornar o catálogo acessível**, confirma que a pasta de origem contém exclusivamente nomes e arquivos cuja listagem pública é permitida, inclusive os uploads futuros. Essa autorização não é presumida pelo simples início desta tarefa.
5. Testa `https://suporte.technolife.net.br/links/`, abertura via `/links`, pesquisa, clipboard, links HTTPS reais e indisponibilidade dos arquivos privados na web; confirma que HESK e `/downloads/` continuam íntegros.
6. Relata o resultado para a homologação operacional e, se necessário, executa o rollback documentado.

**Fluxo:** Codex prepara no notebook → responsável revisa o pacote → responsável publica via cPanel → ambos verificam o resultado → homologação final. Não pedir ao Codex que execute comandos ou altere arquivos no cPanel sem um acesso remoto explicitamente configurado e autorizado, o que **não está disponível nesta etapa**.

## Prioridade operacional

Esta tarefa foi documentada enquanto o Codex executa QA-001, para reduzir o tempo entre homologação e implantação. **Prazo não substitui o gate de segurança** nem é garantia de que a publicação será viável até 15h.

Priorizar o fluxo mínimo funcional já aprovado: lista automática dos arquivos permitidos, pesquisa e copiar link HTTPS. Não implementar novas funcionalidades nem refazer aparência para esta entrega.

## Estrutura de implantação decidida

- **Página da Central:** `https://suporte.technolife.net.br/links/`.
- **Links compartilhados aos clientes:** permanecem em `https://suporte.technolife.net.br/downloads/<arquivo>`; não redirecionar links para `/links/`.
- **Pasta web nova:** `links/`, como irmã de `downloads/` na raiz pública do domínio (confirmar no cPanel). A raiz do domínio e HESK não devem ser alterados.
- **Conteúdo visível em `links/`:** apenas os arquivos da camada pública de `public/` (`index.php`, `styles.css`, `app.js` e `assets/`).
- **Código servidor privado:** o leitor `src/DownloadScanner.php` e qualquer configuração com paths reais devem estar **fora da raiz pública inteira do domínio**, não apenas fora de `links/`. Não publicar `tests/`, `docs/`, `tasks/`, `.git/` ou segredos.
- **Ajuste técnico obrigatório:** o `public/index.php` atual inclui `../src/DownloadScanner.php`. Se for copiado para `links/index.php`, esse caminho aponta para um `src/` **dentro da raiz pública do domínio**, o que **não é aceitável**. Antes da publicação, preparar e testar um include seguro para o scanner em diretório privado fora da webroot (sem versionar paths reais); manter os testes locais funcionais. Verificar `open_basedir` e handler PHP.
- **Comportamento da URL:** acessar `/links` deve abrir a mesma página, diretamente ou por redirecionamento canônico para `/links/`; não depender de regras que possam afetar o HESK.

## Antes de publicar — checklist obrigatório

1. Confirmar que QA-001 não revelou falha bloqueante e que os commits estão homologados/integrados à `main`.
2. Confirmar o document root real de `suporte.technolife.net.br` no cPanel e criar **somente** a nova subpasta `links/` (se ainda não existir), respeitando o conteúdo preexistente. O endereço final exigido é `https://suporte.technolife.net.br/links/`; não criar domínio/subdomínio, não alterar HESK nem `/downloads/`.
3. Confirmar como executar PHP nessa subpasta, versão PHP e permissões necessárias.
4. **Manter `src/` e `tests/` fora de qualquer caminho servido publicamente.** A aplicação atual foi concebida com document root em `public/`; numa subpasta cPanel, **não** copiar o repositório inteiro para dentro da webroot sem adaptar e validar o layout. Validar caminhos de `require_once` depois de organizar a implantação.
5. Descobrir, em ambiente autorizado, o caminho físico **real** da pasta que serve `https://suporte.technolife.net.br/downloads/`; confirmar permissão de leitura para o processo PHP, sem revelar o caminho em repositório público ou respostas web.
6. Confirmar a URL-base HTTPS direta de download; ela deve apontar para o mesmo diretório da fonte física.
7. **Gate de exposição pública:** sem login, a página torna nomes e URLs do inventário acessíveis a qualquer visitante que possua o link. Inspecionar arquivos reais e confirmar que **todos** podem ser exibidos publicamente, inclusive os próximos arquivos que forem adicionados. Se houver material restrito, **não publicar o catálogo sem corrigir a fonte ou obter decisão de proteção adequada**.
8. Definir uma forma segura de fornecer `TECHNOLIFE_DOWNLOADS_DIR` e `TECHNOLIFE_DOWNLOADS_BASE_URL` ao **processo PHP web**. Variáveis do terminal nem sempre estão disponíveis sob PHP-FPM/LiteSpeed; validar `getenv` em ambiente web sem imprimir o valor privado. Se necessário, adaptar a configuração por arquivo privado fora da webroot, sem versionar valores reais.
9. Fazer backup do estado anterior da pasta de destino e preparar rollback simples.

## Execução autorizada — somente após os gates

1. Implantar o **conteúdo** de `public/` em `links/` (não criar `links/public/` como URL final). Disponibilizar scanner e configuração somente fora da raiz pública do domínio; ajustar e validar o caminho de include privado com teste em ambiente autorizado antes de liberar o acesso.
2. Conectar a configuração do servidor à origem correta; nenhuma pasta real é usada no GitHub ou nas fixtures.
3. Validar a página em `https://suporte.technolife.net.br/links/` e verificar também `/links` (com ou sem redirecionamento canônico). Executar os smoke tests de produção **sem expor paths privados**:
   - lista mostra apenas arquivos públicos regulares; nunca `.htaccess`, dotfiles, symlinks ou subpastas;
   - campo pesquisar filtra e atualiza contagem;
   - copiar link retorna HTTPS completo;
   - abrir/baixar funciona para um arquivo real permitido;
   - página funciona em desktop e celular;
   - código privado, `/src/`, `/tests/`, arquivos de configuração e repositório Git **não ficam acessíveis pela web**, inclusive em URLs fora de `/links/` dentro do domínio;
   - não houve alteração no funcionamento do HESK, da raiz do site e dos links em `https://suporte.technolife.net.br/downloads/`.
4. Registrar URL pública, estrutura sanitizada do deploy, testes, versão PHP, riscos remanescentes e rollback.
5. Solicitar homologação humana do resultado e atualizar README/roadmap conforme evidências.

## Condições de bloqueio

**Não publicar** se: QA pendente ou reprovação; inventário com nomes/arquivos não autorizados à divulgação; caminho real/permissões indefinidos; HTTPS ou links inválidos; `src`, `tests`, segredos ou metadados Git expostos; risco de interromper o site HESK.

O horário das 15h é um **marco de entrega desejado**, não uma autorização para superar controles nem um compromisso de sucesso antes de verificar a hospedagem.

## Evidências exigidas

- URL final e horário real de disponibilização (se publicada);
- gate de publicabilidade aprovado pelo responsável;
- mapeamento de diretórios **sanitizado**, sem caminhos sensíveis;
- smoke tests realizados e resultado;
- comprovação de que os arquivos existentes continuam funcionando;
- rollback possível;
- status final: CONCLUÍDO, PARCIAL ou BLOQUEADO com motivo.

**Gate de início:** autorizado pelo responsável humano em 09/10/2026 após homologação da QA-001. O Codex está autorizado a preparar e testar o pacote **no notebook**. A configuração e a publicação **no servidor da empresa serão manuais pelo responsável**, após verificação do inventário publicável e confirmação do ambiente. **Não liberar a URL do catálogo ao público antes de confirmar o inventário, proteger código/configuração e concluir os smoke tests.**
