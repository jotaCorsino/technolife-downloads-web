# 03 — Roadmap e acompanhamento

## Estado em 09/10/2026

**Escopo simplificado:** catálogo automático, apenas leitura, sem autenticação ou persistência própria. Fundação documental atualizada. Implementação funcional não iniciada.

| Ordem | ID | Entrega | Estado |
| --- | --- | --- | --- |
| 0 | INIT-001 | Fundação documental e revisão de escopo | Aguardando revisão |
| 1 | BOOT-001 | Vincular a pasta local ao GitHub com segurança | Aguardando homologação — execução técnica concluída |
| 2 | SCAN-001 | Leitor PHP da pasta de downloads, filtro e URLs seguras | Planejada |
| 3 | UI-001 | Página responsiva, busca e botão de copiar | Planejada |
| 4 | QA-001 | Testes de nomes/URLs, exclusões, atualização e estados de erro | Planejada |
| 5 | DEP-001 | Validar exposição pública e implantar no cPanel | Planejada |

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
