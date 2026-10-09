# 01 — Escopo e critérios do MVP

## Propósito

Oferecer um índice automático e pesquisável dos arquivos de download hospedados pela Technolife, para que os técnicos encontrem e copiem o endereço HTTPS correto sem cadastrá-lo manualmente.

## Origem dos dados

**Fonte única:** arquivos existentes diretamente no diretório `/downloads/` da hospedagem.

- O PHP consulta o diretório no momento da requisição.
- Arquivos adicionados/removidos são refletidos na próxima atualização ou reabertura da página.
- O nome de cada arquivo é usado para construir o título visível e a URL direta; o arquivo físico não é modificado.
- O catálogo não mantém banco ou cópia própria dos registros.
- Na primeira versão, não percorre subdiretórios.

## Interface

Página única com cabeçalho, campo de pesquisa, listagem e ações de **Copiar link** e **Abrir/Baixar**. Títulos derivados dos nomes, extensão/formato identificável e feedback simples. Layout responsivo e visual coerente com a Technolife.

## Requisitos e critérios verificáveis

| ID | Requisito | Aceite |
| --- | --- | --- |
| MVP-01 | Ler diretório | A página lista arquivos comuns diretamente em `/downloads/` sem cadastro |
| MVP-02 | Ignorar arquivos não exibíveis | `.htaccess`, dotfiles, diretórios e symlinks não aparecem |
| MVP-03 | Normalizar títulos | `Technolife-RustDesk-Windows.zip` é exibido como `Technolife RustDesk Windows`, mantendo a extensão identificável |
| MVP-04 | URL correta | Cada item gera link HTTPS absoluto para o próprio arquivo, com o nome devidamente codificado |
| MVP-05 | Pesquisa | Filtra instantaneamente por nome/título, ignorando diferença entre maiúsculas/minúsculas |
| MVP-06 | Cópia | Copia a URL completa para o clipboard e informa o resultado |
| MVP-07 | Acesso ao download | Um clique abre a URL direta, sem passar por upload/gerenciador do painel |
| MVP-08 | Atualização | Incluir/remover arquivo da pasta reflete-se após recarregar a página |
| MVP-09 | Layout e estados | Página responsiva e mensagens legíveis para pasta vazia, erro de leitura e ausência de resultados |

## Fora de escopo

- Cadastro, edição, exclusão ou upload de arquivos pelo painel.
- Formulários de administração, autenticação própria, integração HESK ou permissões por usuário.
- Banco de dados, JSON de cadastro, sincronização periódica ou tarefas agendadas.
- Categorização manual, dashboards, monitoramento de download e encurtador de links.
- Navegação recursiva em subpastas.

## Segurança e publicação

Não haverá login. Portanto, **qualquer pessoa com acesso à URL da Central poderá visualizar a relação de nomes e links**. A versão pública deve ser liberada somente após comprovar que esses dados podem ser exibidos sem restrições. A existência de um link direto não implica que o arquivo deva necessariamente aparecer num índice público: essa liberação deve ser deliberada.

Exibir apenas arquivos regulares não ocultos, não seguir symlinks e não revelar caminhos físicos. O nome de arquivo deve ser tratado como dado não confiável ao ser renderizado.

## Critério de conclusão

Demonstrar pesquisa e cópia, correta formação de URL (inclusive nomes com espaços/caracteres especiais), abertura de downloads, alteração automática da lista mediante inclusão/remoção de arquivos, exclusão dos dotfiles e tratamento seguro de erros. Confirmar que todos os itens exibidos podem ser listados publicamente. A homologação final pertence ao responsável humano.
