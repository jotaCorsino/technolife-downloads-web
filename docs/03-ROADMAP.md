# 03 — Roadmap e acompanhamento

## Estado em 09/10/2026

**Fase atual:** fundação documental publicada, aguardando revisão humana e bootstrap local. **Implementação funcional:** não iniciada.

As etapas seguem o Método ÓRBITA: planejamento → implementação autorizada → testes/evidências → homologação humana → avanço.

| Ordem | ID | Entrega | Estado |
| --- | --- | --- | --- |
| 0 | INIT-001 | Fundação documental no GitHub | Aguardando revisão |
| 1 | BOOT-001 | Bootstrap Git seguro na pasta local | Planejada |
| 2 | UI-001 | Protótipo visual responsivo com dados fictícios | Planejada |
| 3 | SEC-001 | Prova de autorização STAFF e validação do ambiente PHP | Planejada |
| 4 | DATA-001 | Definir e validar persistência compartilhada | Planejada |
| 5 | API-001 | API PHP protegida para CRUD dos links | Planejada |
| 6 | UI-002 | Integrar interface ao backend (CRUD, busca e cópia) | Planejada |
| 7 | QA-001 | Testes funcionais, negativos e homologação do MVP | Planejada |
| 8 | DEP-001 | Implantação controlada e documentação operacional | Planejada |

A ordem pode ser ajustada por decisão humana registrada. Nenhuma etapa autoriza automaticamente a seguinte.

## Gates

**G0 — Fundação:** README, escopo, arquitetura preliminar, roadmap, riscos, regras e tarefa de bootstrap disponíveis no GitHub.

**G1 — Ambiente vinculado:** pasta local correta, `origin` validado, `main` sincronizada, documentação presente e working tree inspecionada. Nenhuma funcionalidade criada no bootstrap.

**G2 — Interface:** prévia aprovada visualmente pelo responsável, sem simular que há persistência real.

**G3 — Segurança e ambiente:** backend capaz de rejeitar corretamente usuários não autorizados e prova técnica da sessão STAFF; escolha de persistência confirmada.

**G4 — MVP funcional:** operações integradas, testes e evidências técnicas apresentados, incluindo cenários de falha e acesso indevido.

**G5 — Publicação:** revisão de riscos, implantação e homologação humana.

## Estados de tarefa

`PLANEJADA` → `AUTORIZADA` → `EM_IMPLEMENTACAO` → `EM_VALIDACAO` → `AGUARDANDO_HOMOLOGACAO` → `CONCLUIDA`.

Uma falha de teste ou reprovação retorna a tarefa ao estado anterior pertinente. Nunca marcar concluída apenas por ter gerado código.

## Fora do planejamento vigente

Upload de arquivos, gestão de executáveis, integração com WhatsApp, métricas, relatórios, categorias e novos perfis de login. Novas ideias poderão ser avaliadas separadamente, sem ampliação silenciosa do MVP.
