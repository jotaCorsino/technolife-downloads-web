# DEP-001 — Implantação controlada da Central de Links

**Estado:** PLANEJADA — preparação documental antecipada em 09/10/2026; **execução ainda NÃO autorizada**.
**Prazo operacional solicitado:** 09/10/2026, **até 15h (America/Sao_Paulo)**.
**Objetivo:** disponibilizar a Central de Links para uso dos técnicos da Technolife na hospedagem existente.
**Pré-requisitos:** QA-001 concluída/homologada e autorização humana explícita para publicar.

## Prioridade operacional

Esta tarefa foi documentada enquanto o Codex executa QA-001, para reduzir o tempo entre homologação e implantação. **Prazo não substitui o gate de segurança** nem é garantia de que a publicação será viável até 15h.

Priorizar o fluxo mínimo funcional já aprovado: lista automática dos arquivos permitidos, pesquisa e copiar link HTTPS. Não implementar novas funcionalidades nem refazer aparência para esta entrega.

## Antes de publicar — checklist obrigatório

1. Confirmar que QA-001 não revelou falha bloqueante e que os commits estão homologados/integrados à `main`.
2. Conferir o destino em cPanel, sem modificar a raiz atual do domínio de suporte ou comprometer HESK. Escolher **subpasta exclusiva** e URL HTTPS que não conflitem com `/downloads/`, HESK ou projetos existentes.
3. Confirmar como executar PHP nessa subpasta, versão PHP e permissões necessárias.
4. **Manter `src/` e `tests/` fora de qualquer caminho servido publicamente.** A aplicação atual foi concebida com document root em `public/`; numa subpasta cPanel, **não** copiar o repositório inteiro para dentro da webroot sem adaptar e validar o layout. Validar caminhos de `require_once` depois de organizar a implantação.
5. Descobrir, em ambiente autorizado, o caminho físico **real** da pasta que serve `https://suporte.technolife.net.br/downloads/`; confirmar permissão de leitura para o processo PHP, sem revelar o caminho em repositório público ou respostas web.
6. Confirmar a URL-base HTTPS direta de download; ela deve apontar para o mesmo diretório da fonte física.
7. **Gate de exposição pública:** sem login, a página torna nomes e URLs do inventário acessíveis a qualquer visitante que possua o link. Inspecionar arquivos reais e confirmar que **todos** podem ser exibidos publicamente, inclusive os próximos arquivos que forem adicionados. Se houver material restrito, **não publicar o catálogo sem corrigir a fonte ou obter decisão de proteção adequada**.
8. Definir uma forma segura de fornecer `TECHNOLIFE_DOWNLOADS_DIR` e `TECHNOLIFE_DOWNLOADS_BASE_URL` ao **processo PHP web**. Variáveis do terminal nem sempre estão disponíveis sob PHP-FPM/LiteSpeed; validar `getenv` em ambiente web sem imprimir o valor privado. Se necessário, adaptar a configuração por arquivo privado fora da webroot, sem versionar valores reais.
9. Fazer backup do estado anterior da pasta de destino e preparar rollback simples.

## Execução autorizada — somente após os gates

1. Implantar os arquivos mínimos: interface de `public/` no destino web escolhido e componente PHP do scanner num local fora do alcance HTTP (ou implantar com document root apontando estritamente para `public/`).
2. Conectar a configuração do servidor à origem correta; nenhuma pasta real é usada no GitHub ou nas fixtures.
3. Validar a página em HTTPS e executar os smoke tests de produção **sem expor paths privados**:
   - lista mostra apenas arquivos públicos regulares; nunca `.htaccess`, dotfiles, symlinks ou subpastas;
   - campo pesquisar filtra e atualiza contagem;
   - copiar link retorna HTTPS completo;
   - abrir/baixar funciona para um arquivo real permitido;
   - página funciona em desktop e celular;
   - `/src/`, `/tests/`, arquivos de configuração e repositório Git **não ficam acessíveis pela web**;
   - não houve alteração no funcionamento do HESK e dos downloads já existentes.
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

**Gate:** implantação depende de homologação da QA-001 e autorização explícita para DEP-001. Nenhuma ação de servidor é autorizada por esta documentação.
