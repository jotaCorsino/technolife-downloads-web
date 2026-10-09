# 04 — Regras locais de desenvolvimento e homologação

O projeto segue o [Método ÓRBITA](https://github.com/jotaCorsino/orbita-development-model).

## Identidade

- **Nome do produto:** Technolife — Central de Links.
- **Repositório:** `jotaCorsino/technolife-downloads-web`.
- **Branch principal:** `main`.
- **Pasta local recomendada:** `technolife-downloads-web`.
- **Planejamento:** ChatGPT.
- **Implementação:** Codex na working copy local.
- **Decisão e homologação:** responsável humano.

## Regras operacionais

1. A documentação persistente do GitHub prevalece sobre lembranças de chats antigos.
2. Toda tarefa funcional tem ID, objetivo, escopo incluído/excluído, critérios de aceite e evidências esperadas.
3. A primeira tarefa é **somente** a sincronização local `BOOT-001`. Não implementar funcionalidade no bootstrap.
4. Mudanças funcionais usam branch própria e PR revisável, quando aplicável.
5. Não realizar merge, implantação, exclusão destrutiva ou avanço de tarefa sem o gate humano correspondente.
6. O agente implementador não homologa sua própria entrega.
7. Preferir pequenas alterações e manter documentação alinhada ao estado aprovado.
8. Não versionar senhas, tokens, sessões, dados reais, arquivos executáveis ou configurações operacionais internas.

## Convenção de Git

Branches de exemplo: `feat/UI-001-painel`, `feat/SEC-001-hesk-staff`, `fix/ID-descricao`, `docs/ID-descricao`.

Commits descrevem a intenção: `docs:`, `feat:`, `fix:`, `test:`, `chore:`.

A `main` deve refletir uma base conhecida. A fundação inicial de um repositório vazio é o preparo do projeto, não uma release funcional.

## Evidências por entrega

Resumo do que mudou, arquivos, branch, SHA do commit, PR quando houver, testes executados e resultados, riscos verificados, pontos não validados e instruções de revisão/homologação.

## Homologação

Resultados aceitos: **APROVADO**, **APROVADO COM RESSALVAS**, **CORREÇÃO NECESSÁRIA**, **REPLANEJAMENTO NECESSÁRIO**.

Homologação do MVP exige operações completas e validação de segurança proporcional ao acesso de técnicos. Testes positivos, por si, não dispensam tentativas negativas (sessão ausente, expirada e requisições não autorizadas).

## Publicação pública

Este repositório é público. Material operacional específico, logs, dados de clientes, credenciais e configurações da hospedagem devem ficar fora dele. Quando um detalhe privado for necessário à implantação, documentá-lo no ambiente interno autorizado, não no GitHub público.
