# SCAN-001 — Leitor PHP automático da pasta de downloads

**Estado:** AUTORIZADA em 09/10/2026 — implementação ainda não iniciada.
**Etapa:** primeira funcionalidade do MVP, após BOOT-001.
**Agente:** Codex, trabalhando na pasta local `technolife-downloads-web`.

## Identidade e base

- **Projeto:** Technolife — Central de Links.
- **Repositório remoto:** `https://github.com/jotaCorsino/technolife-downloads-web.git`.
- **Base:** `origin/main` atualizada, após a documentação/homologação da BOOT-001.
- **Branch de implementação:** `feat/SCAN-001-leitor-downloads`.
- **Documentos:** `README.md`, `docs/01-ESCOPO.md`, `docs/02-ARQUITETURA.md`, `docs/03-ROADMAP.md` e `docs/04-GOVERNANCA.md`.

## Objetivo

Implementar um **leitor PHP pequeno, testável e somente leitura** que consulte os arquivos existentes diretamente em um diretório configurado, derive nomes de exibição e gere as URLs HTTPS completas do download. Essa função será usada pela interface em tarefa posterior (UI-001).

**Não cadastrar nem alterar arquivos:** `/downloads/` é a fonte única. A inclusão/remoção feita externamente se reflete na próxima execução do leitor.

## Escopo incluído

1. Uma implementação PHP reutilizável para receber **caminho de diretório** e **URL-base HTTPS dos downloads** por configuração confiável do servidor (ou parâmetros internos de função), **jamais** de entradas web do visitante.
2. Ler somente o nível imediato do diretório, sem recursão.
3. Listar exclusivamente **arquivos regulares**, ignorando:
   - `.htaccess`;
   - qualquer nome iniciado por ponto (`.`, `..`, `.env`, etc.);
   - subpastas e links simbólicos, mesmo que apontem para arquivo regular.
4. Retornar para cada arquivo dados mínimos: `filename` (nome original), `title` (título normalizado para exibição), `extension` (indicador de formato, quando disponível) e `url` (HTTPS absoluto).
5. Normalizar **somente o título apresentado**: separar nome e extensão final, substituir `-` e `_` por espaços e colapsar espaços extras, preservando acentos, versões e letras originais. Ex.: `Technolife-RustDesk-Windows.zip` → `Technolife RustDesk Windows` / `ZIP`.
6. Formar a URL preservando integralmente o nome real do arquivo como um **único segmento codificado** (ex.: `rawurlencode` no PHP), sem renomear o arquivo no disco.
7. Ordenar resultados de modo previsível pelo título, com desempate pelo nome original.
8. Tratar com segurança pasta inexistente, leitura negada, URL-base inválida, pasta vazia e nomes problemáticos; não revelar paths internos em mensagens que possam ser exibidas em produção.
9. Escrever testes automatizados PHP ou um teste CLI reproduzível com **fixtures locais fictícias** cobrindo casos positivos e negativos. Evitar dependências externas desnecessárias.

## Fora do escopo

- Interface definitiva, CSS, busca JavaScript, clipboard e botão de abrir/baixar (UI-001).
- Deploy, configuração real do cPanel, acesso remoto à hospedagem ou leitura de arquivos reais de clientes.
- Upload, edição, exclusão, CRUD, banco de dados, HESK, autenticação, logs de download ou monitoramento.
- Ativação de índice público / configuração Apache ou LiteSpeed, ou qualquer publicação do inventário real de downloads.
- Alteração de documentação do Método ÓRBITA ou de outros repositórios.

## Restrições e segurança

- Nenhum input de navegador, query string ou header pode escolher a pasta nem a URL-base; não usar `HTTP_HOST` para gerar links.
- Exigir URL-base HTTPS válida, com estrutura apropriada para concatenação de segmentos; não transformar configuração inválida em um link HTTP inesperado.
- Não resolver symlinks para fora da pasta; rejeitá-los antes de incluir arquivos.
- Tratar nomes como dados não confiáveis; o código que montar HTML futuramente deverá escapar o conteúdo. Nesta etapa, separar os dados da apresentação.
- Não retornar `.htaccess` e outros dotfiles, mesmo em caso de erro ou entrada malformada.
- Não versionar arquivos de produção, credenciais nem caminhos físicos privados. Configurações locais de teste podem usar `example.invalid` e diretórios temporários.
- Evitar warnings e stack traces públicos com paths reais; testes podem inspecionar falhas sem vazar esses detalhes.
- **Importante:** catálogo **sem login** é catálogo **público**. A publicação depende de revisão de que todos os nomes e URLs da origem são publicáveis (gate DEP-001); desenvolver esta biblioteca não autoriza exposição do diretório real.

## Critérios de aceite

- [ ] Todos os arquivos regulares diretamente na pasta de teste aparecem uma única vez.
- [ ] `.htaccess`, dotfiles, pastas e symlinks não aparecem.
- [ ] `filename` conserva nome original; título e extensão estão corretos.
- [ ] URLs resultam em HTTPS absoluto, com espaços, acentos, `#`, `?` e outros caracteres reservados codificados como segmento.
- [ ] Pasta vazia retorna coleção vazia; pasta ausente ou inacessível retorna falha controlada e sem exposição de filesystem.
- [ ] Incluir ou remover arquivo na pasta fictícia altera o resultado de nova leitura.
- [ ] A função não cria, move, apaga nem renomeia nenhum arquivo.
- [ ] Execução dos testes ou roteiro CLI demonstrada com resultado explícito.
- [ ] Branch/commit e diff coerentes com o escopo; nenhum código funcional enviado à `main` sem revisão/homologação.

## Evidências exigidas do Codex

1. Resumo técnico da solução e da localização dos arquivos.
2. Branch, commit SHA e PR (quando aplicável).
3. Comando e resultado de todos os testes.
4. Demonstração com fixtures de arquivos normais, ocultos, subpastas, symlinks e nomes especiais.
5. Exemplos de título/extensão/URL gerados usando URLs fictícias.
6. Limitações e riscos restantes, inclusive diferenças entre ambiente local e cPanel.

**Gate:** após implementar, testar e apresentar evidências, **parar em AGUARDANDO_HOMOLOGACAO**. Não iniciar UI-001 nem implantar no servidor.
