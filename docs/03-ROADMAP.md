# 03 — Roadmap e acompanhamento

## Estado em 09/10/2026

**Escopo simplificado:** catálogo automático, apenas leitura, sem autenticação ou persistência própria. **INIT-001, BOOT-001, SCAN-001 e UI-001 homologadas. PR #2 integrado à `main`. QA-001 validada localmente e aguardando homologação; pendências de cPanel registradas no [relatório de QA](06-RELATORIO-QA.md).**

| Ordem | ID | Entrega | Estado |
| --- | --- | --- | --- |
| 0 | INIT-001 | Fundação documental e revisão de escopo | 🟢 Concluído — aprovado |
| 1 | BOOT-001 | Vincular a pasta local ao GitHub com segurança | 🟢 Concluído — homologado |
| 2 | [SCAN-001](../tasks/SCAN-001-LEITOR-DOWNLOADS.md) | Leitor PHP da pasta de downloads, filtro e URLs seguras | 🟢 Concluído — homologado, PR #1 integrado |
| 3 | [UI-001](../tasks/UI-001-INTERFACE-CATALOGO.md) | Página responsiva, busca e botão de copiar | 🟢 Concluído — homologado, PR #2 integrado |
| 4 | [QA-001](../tasks/QA-001-VALIDACAO-MVP.md) | Testes funcionais, segurança e preparação de implantação | 🔵 Aguardando homologação |
| 5 | DEP-001 | Validar exposição pública e implantar no cPanel | ⚪ Não iniciado |

**Legenda:** ⚪ Não iniciado · 🟡 Em andamento · 🔵 Em validação/aguardando homologação · 🟢 Concluído (homologado) · 🟠 Pausado. A conclusão técnica não equivale à homologação humana.

Não há tarefas de CRUD, sessão STAFF, cadastro de links nem banco de dados. Essas frentes foram **canceladas por simplificação do escopo** antes de qualquer implementação.

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
