# 04 — Regras locais de desenvolvimento e homologação

O projeto segue o [Método ÓRBITA](https://github.com/jotaCorsino/orbita-development-model).

## Identidade

- **Nome:** Technolife — Central de Links
- **Repositório:** `jotaCorsino/technolife-downloads-web`
- **Branch principal:** `main`
- **Pasta local:** `technolife-downloads-web`
- **Planejamento:** ChatGPT
- **Implementação:** Codex
- **Decisão/homologação:** responsável humano

## Regras

1. O GitHub é a referência persistente. Antes de trabalhar, consultar documentação e estado atual.
2. Desenvolver o **catálogo automático somente leitura**, sem ampliar o escopo silenciosamente.
3. Primeiro executar `BOOT-001` para sincronizar a pasta local; **não implementar funcionalidades no bootstrap**.
4. Após o bootstrap, trabalhar por tarefas com ID, escopo, critérios de aceite e evidências.
5. Usar branch específica e PR revisável para mudanças funcionais quando aplicável.
6. Não executar merge, deploy ou destruição de dados sem gate e homologação humana.
7. Nenhuma tarefa está homologada só porque o código executou; testes e riscos precisam ser reportados.
8. Não versionar arquivos reais, segredos, credenciais, logs privados ou caminhos internos sensíveis.

## Convenções

Branches exemplificativas: `feat/SCAN-001-leitor-downloads`, `feat/UI-001-catalogo`, `fix/ID-descricao`, `docs/ID-descricao`.

Commits descrevem a intenção com `docs:`, `feat:`, `fix:`, `test:` ou `chore:`.

## Homologação

Resultados possíveis: **APROVADO**, **APROVADO COM RESSALVAS**, **CORREÇÃO NECESSÁRIA**, **REPLANEJAMENTO NECESSÁRIO**.

Evidências esperadas por entrega: resumo do diff, branch, SHA, PR se aplicável, testes funcionais, casos negativos (dotfiles, symlinks, erros de leitura, URLs especiais), limitações e riscos.

## Segurança da publicação

A Central não exigirá login. Portanto, antes do deploy precisa ser validado que **a publicação de todos os nomes e URLs exibidos é permitida**. Esconder um link ou desconhecer a URL não equivale a controlar seu acesso. Caso o diretório contenha material restrito, esse risco precisa de decisão humana antes de colocar o índice no ar.

A aplicação não deve permitir editar, apagar ou enviar arquivos. As operações de arquivos continuam externas ao painel.
