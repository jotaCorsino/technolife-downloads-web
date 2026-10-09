# Technolife — Central de Links

**Catálogo web automático de links de download** de programas, instaladores e ferramentas hospedados pela Technolife. Feito para que a equipe de suporte encontre rapidamente o arquivo certo, clique no link ou copie sua URL HTTPS para enviar ao cliente.

## Acompanhamento do desenvolvimento

**Atualizado em:** 09/10/2026  
**Fase atual:** QA-001 — validada localmente, aguardando homologação.
**Etapas homologadas:** INIT-001, BOOT-001, SCAN-001 e UI-001. PR #2 integrado em 09/10/2026.

| Ordem | ID | Etapa | Status |
| --- | --- | --- | --- |
| 0 | **INIT-001** | Fundação documental e definição do escopo | 🟢 Concluído |
| 1 | **[BOOT-001](tasks/BOOT-001-BOOTSTRAP-LOCAL.md)** | Preparação e sincronização do ambiente local | 🟢 Concluído |
| 2 | **[SCAN-001](tasks/SCAN-001-LEITOR-DOWNLOADS.md)** | Leitor PHP automático da pasta `/downloads/` | 🟢 Concluído |
| 3 | **[UI-001](tasks/UI-001-INTERFACE-CATALOGO.md)** | Interface responsiva, pesquisa e cópia de links | 🟢 Concluído |
| 4 | **[QA-001](tasks/QA-001-VALIDACAO-MVP.md)** | Testes funcionais e validações de segurança | 🔵 Aguardando homologação |
| 5 | **DEP-001** | Implantação e homologação final | ⚪ Não iniciado |

**Legenda:** ⚪ Não iniciado · 🟡 Em andamento · 🔵 Em validação / aguardando homologação · 🟢 Concluído (homologado) · 🟠 Pausado.

**Próxima ação:** revisar o [relatório da QA-001](docs/06-RELATORIO-QA.md) e homologar ou solicitar correções. Publicação no cPanel exige decisão separada sobre os bloqueadores documentados; ainda não realizar deploy.

Os detalhes, critérios de aceite e gates estão no [roadmap completo](docs/03-ROADMAP.md). **Atualizar esta tabela e o roadmap juntos** sempre que o estado mudar.

## Problema

Os arquivos já estão disponíveis na pasta `/downloads/` da hospedagem. Durante os atendimentos, os técnicos precisam descobrir e compartilhar seus endereços completos. Criar e manter manualmente uma segunda lista de links acrescentaria trabalho desnecessário.

## Solução

Uma página enxuta, somente leitura, que **consulta os arquivos existentes em `/downloads/`**, normaliza os nomes para apresentação, permite pesquisa instantânea e oferece **Abrir/Baixar** e **Copiar link**.

Quando um arquivo é adicionado ou removido da pasta pela operação habitual da hospedagem, ele aparece ou desaparece da lista na próxima atualização da página. Não há registros a cadastrar.

**Não existem formulários de cadastro, edição, exclusão, banco de dados, upload de arquivos, novo login ou integração com a sessão do HESK.**

## MVP

| Recurso | Comportamento |
| --- | --- |
| Listagem automática | Ler apenas arquivos comuns diretamente de `/downloads/` |
| Filtragem de segurança | Ignorar `.htaccess`, outros itens ocultos, subpastas e links simbólicos |
| Título normalizado | Derivar um título legível do nome do arquivo, preservando o nome real na URL |
| Pesquisa instantânea | Filtrar localmente por título ou nome de arquivo, sem recarregar a página |
| Copiar link | Copiar a URL HTTPS completa em um clique, com feedback discreto |
| Abrir/Baixar | Abrir o link direto do arquivo; o comportamento de download depende do servidor/navegador |
| Atualização | Refletir o conteúdo atual da pasta a cada carregamento/atualização da página |
| Responsividade | Funcionar em computadores e dispositivos móveis |

## Tecnologias

- **HTML e CSS:** interface minimalista, inspirada nos aplicativos internos da Technolife.
- **JavaScript puro:** pesquisa instantânea e botão de copiar.
- **PHP mínimo:** lê o diretório no servidor e gera a lista; o navegador não acessa pastas do servidor diretamente.
- **Hospedagem existente:** arquivos permanecem em `/downloads/`; a Central não realiza upload nem hospeda executáveis.

**Sem persistência própria ou autenticação no aplicativo.** A pasta de downloads já é a fonte de dados.

## Visibilidade e segurança

**Sem login significa catálogo publicamente acessível.** O sistema só deve ser publicado nesse formato se **os nomes e os links de todos os arquivos exibidos puderem ser divulgados publicamente**. Links diretos dos downloads também permanecem acessíveis conforme a configuração da hospedagem.

O código deve:

- listar somente arquivos regulares de uma pasta fixa, sem recursão;
- ignorar `.htaccess` e demais nomes iniciados por ponto;
- não seguir symlinks;
- usar uma base HTTPS de download configurada pelo servidor, e não entrada do visitante;
- codificar corretamente nomes de arquivo nas URLs e escapar texto apresentado na página;
- não expor caminhos físicos do servidor nem detalhes internos nos erros;
- tratar diretório ausente ou inacessível com mensagem genérica e sem listar nada.

Os arquivos exibidos são gerenciados na hospedagem, fora da Central de Links.

## Execução local com arquivos fictícios

Use PHP 8+ e uma pasta temporária criada para o teste. Node.js 18+ é necessário somente para o teste do JavaScript. O servidor web deve apontar para `public/`, deixando `src/` e `tests/` fora da raiz pública.

```bash
fixture_dir=$(mktemp -d)
printf 'exemplo\n' > "$fixture_dir/Technolife-RustDesk-Windows.zip"
printf 'exemplo\n' > "$fixture_dir/Olá Mundo #1?.tar.gz"
TECHNOLIFE_DOWNLOADS_DIR="$fixture_dir" \
TECHNOLIFE_DOWNLOADS_BASE_URL='https://example.invalid/downloads' \
php -S 127.0.0.1:8000 -t public
```

Abra `http://127.0.0.1:8000/`. Pesquise por `ola`, confira a contagem e o estado sem resultados, e use o ícone de copiar link. O endereço `example.invalid` é fictício: o ícone de abrir/baixar aponta diretamente para o URL gerado, mas não baixa um arquivo real neste exemplo. Os ícones têm nomes acessíveis e dicas ao passar o cursor. Para testar a pasta vazia, inicie o servidor com uma pasta temporária sem arquivos; para testar a mensagem de erro, inicie sem as duas variáveis. A página não escolhe diretório ou URL por parâmetros do visitante. Em produção, a página deve ser acessada por HTTPS para o Clipboard API funcionar nos navegadores compatíveis.

Validações automatizadas locais:

```bash
php -l public/index.php
php -l tests/InterfaceTest.php
php tests/InterfaceTest.php
node --check public/app.js
node tests/InterfaceScriptTest.js
php tests/DownloadScannerTest.php
```

Não há dependências de terceiros nem build. Os links **Abrir / baixar** usam a mesma aba; o navegador e o servidor definem se o arquivo abre ou é baixado.

## Documentação

- [Escopo e aceite](docs/01-ESCOPO.md)
- [Arquitetura e leitura de arquivos](docs/02-ARQUITETURA.md)
- [Roadmap](docs/03-ROADMAP.md)
- [Governança ÓRBITA](docs/04-GOVERNANCA.md)
- [Decisões e riscos](docs/05-DECISOES-E-RISCOS.md)
- [Relatório de QA do MVP](docs/06-RELATORIO-QA.md)
- [BOOT-001 — Bootstrap local seguro](tasks/BOOT-001-BOOTSTRAP-LOCAL.md)
- [SCAN-001 — Leitor PHP automático](tasks/SCAN-001-LEITOR-DOWNLOADS.md)
- [UI-001 — Interface do catálogo](tasks/UI-001-INTERFACE-CATALOGO.md)
- [QA-001 — Validação do MVP](tasks/QA-001-VALIDACAO-MVP.md)

## Método ÓRBITA

Desenvolvimento assistido por IA pelo [Método ÓRBITA](https://github.com/jotaCorsino/orbita-development-model): **Planejar → Executar → Evidenciar → Homologar**. ChatGPT organiza o planejamento, Codex implementa tarefas autorizadas e o responsável humano homologa.
