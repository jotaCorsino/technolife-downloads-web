# BOOT-001 — Bootstrap local seguro

**Estado:** PLANEJADA — primeira tarefa de implementação, sem funcionalidade.

## Identidade

- **Produto:** Technolife — Central de Links
- **Repositório:** https://github.com/jotaCorsino/technolife-downloads-web
- **Branch principal esperada:** `main`
- **Pasta local esperada:** `technolife-downloads-web`
- **Agente implementador:** Codex

## Objetivo

Transformar a pasta local já criada pelo responsável humano em working copy segura do repositório remoto que contém a documentação inicial do projeto.

## Incluído

1. Inspecionar o caminho atual e confirmar que a pasta corresponde a este projeto.
2. Verificar se já existe repositório Git. Inicializar apenas se não existir.
3. Verificar se o remoto `origin` existe e se aponta para a URL correta.
4. Buscar estado remoto, sincronizar `main` sem destruir arquivos locais únicos ou histórico divergente.
5. Confirmar que README, `docs/` e `tasks/` estão presentes.
6. Retornar evidências de pasta, `origin`, branch, HEAD, `git status` e divergências; parar.

## Fora do escopo

Implementação de HTML/CSS/JS/PHP, testes funcionais, alterações em arquitetura, instalação de dependências, publicação de site, exclusão/renomeação de arquivos do usuário e criação de funcionalidade.

## Segurança e conflitos

Se houver arquivos que conflitem, remoto incorreto, histórico divergente ou necessidade de `reset --hard`/`force push`, **interromper e reportar**; não resolver silenciosamente. Não versionar segredos nem dados locais reais.

## Critérios de aceite

- [ ] Diretório local e repositório remoto correspondem ao projeto certo.
- [ ] `origin` aponta para `jotaCorsino/technolife-downloads-web`.
- [ ] `main` remota e working copy estão sincronizadas com segurança.
- [ ] README e documentação da fundação foram obtidos.
- [ ] Branch, HEAD e `git status` foram informados.
- [ ] Nenhuma funcionalidade foi implementada e nenhuma ação destrutiva executada.

## Evidências esperadas

Caminho completo da pasta (sem expor informações sensíveis), estado prévio do Git, URL de `origin`, branch atual, SHA de HEAD, `git status`, arquivos documentais presentes, eventuais bloqueios. Após entregar evidências, **parar para análise e homologação humana**.

## Próximo passo

Somente após esse gate o planejamento poderá preparar a primeira tarefa funcional (UI-001) para autorização.
