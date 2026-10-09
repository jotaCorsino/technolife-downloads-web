# 02 — Arquitetura — catálogo somente leitura

> Arquitetura planejada, ainda não validada no ambiente real.

## Fluxo

```text
         Hospedagem
      /downloads/ (arquivos)
               |
     Leitura local de diretório
               |
       PHP (somente leitura)
               |
    HTML + títulos + URLs HTTPS
               |
       Página no navegador
         |           |
     Busca JS     Copiar link
```

Não há banco de dados, endpoint de escrita, cadastro, autenticação nem comunicação com o HESK.

## Componentes

**PHP** — camada mínima de servidor para listar os arquivos. Navegadores não têm permissão de consultar diretamente o filesystem do servidor. PHP consulta diretório fixo, aplica filtros e produz dados seguros à renderização.

**HTML/CSS** — exibe os itens em uma única página, responsiva e com identidade visual Technolife.

**JavaScript** — filtra os itens já carregados e usa Clipboard API (com tratamento de falha) para copiar URLs.

## Fonte de arquivos

- Diretório de leitura: `/downloads/` no site de hospedagem, mapeado para **caminho absoluto de filesystem pelo servidor**, não fornecido em query string.
- A pasta efetiva será confirmada no deploy; não publicar paths privados no repositório.
- Somente itens diretamente contidos no diretório.
- Selecionar **arquivos regulares**. Ignorar entradas cujo nome comece com `.` (inclusive `.htaccess`), subdiretórios e links simbólicos.
- Arquivos adicionados ou removidos aparecem/desaparecem na próxima requisição da página; não é necessário polling em tempo real.
- Erros de permissão e pasta inexistente devem retornar estado genérico, sem revelar paths ou detalhes internos.

## Normalização do nome

A transformação ocorre **somente na interface**:

1. Preservar o nome real para URL.
2. Identificar a extensão final do arquivo (quando houver).
3. Remover essa extensão para formar o título.
4. Substituir separadores `-` e `_` por espaços e normalizar espaços repetidos.
5. Preservar letras, acentos, números e versões relevantes; não forçar mudanças imprecisas de capitalização.
6. Ordenar por título de forma estável.

Exemplo: `Technolife-RustDesk-Windows.zip` → título `Technolife RustDesk Windows`, extensão `ZIP`, URL final mantida com o nome original.

## Construção de URLs

O servidor define **uma base HTTPS de downloads autorizada**. Para cada item, concatenar à base o nome de arquivo codificado como segmento de URL (equivalente a `rawurlencode` em PHP). Não usar path físico na URL, não confiar em host/query string escolhidos pelo visitante, nem produzir URLs para caminhos fora da pasta.

Links devem apontar **diretamente** para os arquivos servidos pela hospedagem. O comportamento ao abrir (download imediato ou apresentação pelo navegador) pode variar conforme cabeçalhos e formato do arquivo.

## Segurança e limites

Esta ferramenta **não autentica usuários**. A página publicada será um índice público dos arquivos. Antes do deploy, verificar que todos os nomes e URLs listados são publicáveis; conteúdos confidenciais ou de distribuição restrita não devem estar na origem usada pelo índice público.

- Nenhuma entrada do usuário determina o diretório consultado.
- Não processar `..` ou caminhos fornecidos por parâmetros para ler arquivos.
- Não seguir links simbólicos.
- Escapar qualquer texto ao gerar HTML; não injetar nomes diretamente em `innerHTML`.
- Não revelar nomes de dotfiles, stack traces ou filesystem paths.
- Não fornecer métodos de escrita, exclusão ou upload.
- Manter páginas e URLs HTTPS.
- O código fonte público não deve conter segredos nem informações operacionais privadas.

## Deploy — verificar antes da implantação

- PHP disponível no cPanel.
- Caminho real da pasta `/downloads/` acessível em modo leitura ao processo PHP.
- Base HTTPS correta para links, URL da página e permissões de acesso.
- Comportamento de arquivos contendo espaços, acentos e caracteres especiais.
- Conteúdo do diretório revisado para publicação pública.

A solução não depende de listagem automática do Apache/LiteSpeed nem de habilitar `Indexes`: a leitura é feita localmente pelo PHP.
