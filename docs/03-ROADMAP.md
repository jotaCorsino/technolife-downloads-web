# 03 — Roadmap e acompanhamento

## Estado em 09/10/2026

**Marco de entrega solicitado às 14h12:** disponibilizar a Central de Links para os técnicos **até 15h de 09/10/2026 (America/Sao_Paulo)**. Prazo operacional prioritário, condicionado à QA homologada e à validação de segurança antes do deploy. Ver [DEP-001](../tasks/DEP-001-IMPLANTACAO-CPANEL.md).

**Escopo simplificado:** catálogo automático, apenas leitura, sem autenticação ou persistência própria. **INIT-001, BOOT-001, SCAN-001, UI-001 e QA-001 homologadas. PR #3 integrado à `main`. DEP-001 autorizada para preparação e verificações no cPanel; publicação pública condicionada aos gates descritos no [relatório de QA](06-RELATORIO-QA.md).**

| Ordem | ID | Entrega | Estado |
| --- | --- | --- | --- |
| 0 | INIT-001 | Fundação documental e revisão de escopo | 🟢 Concluído — aprovado |
| 1 | BOOT-001 | Vincular a pasta local ao GitHub com segurança | 🟢 Concluído — homologado |
| 2 | [SCAN-001](../tasks/SCAN-001-LEITOR-DOWNLOADS.md) | Leitor PHP da pasta de downloads, filtro e URLs seguras | 🟢 Concluído — homologado, PR #1 integrado |
| 3 | [UI-001](../tasks/UI-001-INTERFACE-CATALOGO.md) | Página responsiva, busca e botão de copiar | 🟢 Concluído — homologado, PR #2 integrado |
| 4 | [QA-001](../tasks/QA-001-VALIDACAO-MVP.md) | Testes funcionais, segurança e preparação de implantação | 🟢 Concluído — homologado, PR #3 integrado |
| 5 | [DEP-001](../tasks/DEP-001-IMPLANTACAO-CPANEL.md) | Validar exposição pública e implantar no cPanel | 🟡 Autorizada — preparação em andamento |

**Legenda:** ⚪ Não iniciado · 🟡 Em andamento · 🔵 Em validação/aguardando homologação · 🟢 Concluído (homologado) · 🟠 Pausado. A conclusão técnica não equivale à homologação humana.

Não há tarefas de CRUD, sessão STAFF, cadastro de links nem banco de dados. Essas frentes foram **canceladas por simplificação do escopo** antes de qualquer implementação.

## Prioridade da entrega de hoje

1. Finalizar QA-001 e obter evidências e homologação humana.
2. Homologar e integrar PR de QA, se aprovado, preservando a `main`.
3. Confirmar o inventário publicável e a configuração viável no cPanel sem comprometer HESK, links existentes ou arquivos privados.
4. Após autorização explícita, realizar DEP-001, testar pesquisa/cópia/download em HTTPS e obter aceite operacional.

O prazo **não autoriza pular gates**, nem criar uma listagem pública de arquivos não liberados. Se algum item bloquear a publicação segura, registrar o impeditivo e o estado real até as 15h.

## Gates

**G0 — Documentação:** escopo, arquitetura, riscos, roadmap e BOOT-001 revisados.

**G1 — Bootstrap:** pasta local correta, remoto validado, `main` sincronizada, nenhuma funcionalidade implementada na inicialização.

**G2 — Leitura:** teste com pasta fictícia local; apenas arquivos permitidos; títulos e URLs formados corretamente; leitura falha tratada sem exposição de caminhos.

**G3 — Interface:** pesquisa e cópia funcionais, responsividade e feedback visual revisados.

**G4 — Segurança/publicação:** inspeção do diretório real para decidir se um **catálogo sem login pode ser público**; links, arquivo `.htaccess`, dotfiles, symlinks e erros tratados com segurança.

**G5 — Homologação/implantação:** validação do MVP, autorização humana para publicação e documentação final.

As tarefas são pequenas e independentes. Não avançar automaticamente de uma para outra.

## Estados das tarefas

`PLANEJADA` → `AUTORIZADA` → `EM_IMPLEMENTACAO` → `EM_VALIDACAO` → `AGUARDANDO_HOMOLOGACAO` → `CONCLUIDA`.

Qualquer mudança posterior de escopo deve ser registrada antes de implementada.
