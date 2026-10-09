# 01 — Escopo e critérios do MVP

## Propósito

Centralizar links de instaladores, programas e ferramentas usados pela equipe de suporte, reduzindo buscas manuais e padronizando o compartilhamento com clientes.

## Usuários

Técnicos da Technolife previamente autorizados. Clientes recebem URLs diretas de arquivos e **não acessam o painel**.

## Fluxo principal

O técnico abre o painel autenticado, pesquisa um programa por título, identifica o registro e copia o link. O endereço copiado pode ser enviado em outro canal. Quando necessário, o técnico cadastra um título e uma URL; o registro fica disponível para os demais usuários autorizados após salvar.

## Dentro do MVP

| ID | Requisito | Critério verificável |
| --- | --- | --- |
| MVP-01 | Listagem | Mostra registros salvos e estado vazio compreensível |
| MVP-02 | Pesquisa | Filtra por título sem recarregar a página; não diferencia maiúsculas/minúsculas |
| MVP-03 | Copiar link | Copia a URL integral e exibe feedback, inclusive em caso de falha |
| MVP-04 | Cadastro | Exige título e URL válida; registro salvo reaparece ao consultar novamente |
| MVP-05 | Edição | Altera título/URL de registro existente sem criar duplicata por engano |
| MVP-06 | Exclusão | Pede confirmação e remove apenas o registro indicado |
| MVP-07 | Compartilhamento | Modificações feitas por um técnico ficam disponíveis aos outros |
| MVP-08 | Proteção | Visitantes não autorizados não acessam dados nem operações do backend |
| MVP-09 | Responsividade | Painel utilizável em desktop e dispositivo móvel |

## Fora do MVP

Upload de instaladores, gestão de arquivos físicos, hospedagem de executáveis, links encurtados, monitoramento de downloads, rastreamento de clientes, métricas de acesso, relatórios, categorias, etiquetas, permissões granulares por técnico e um login independente.

## Regras funcionais iniciais

- Um registro contém **identificador interno**, **título** e **URL**, além de metadados técnicos mínimos para manutenção quando necessários.
- O formulário pede somente título e URL.
- As alterações persistem no servidor, nunca exclusivamente no armazenamento local do navegador.
- O link compartilhado deve preservar a URL cadastrada; o painel não modifica nem redireciona o arquivo.
- Mensagens de sucesso e erro são claras e discretas.
- Não expor dados do catálogo antes da autorização do usuário.

## Critério de conclusão do produto

O MVP só pode ser homologado após demonstração funcional da pesquisa, cópia, cadastro, edição, exclusão, persistência entre sessões e bloqueio efetivo de acesso não autorizado. Os testes devem contemplar entradas inválidas, tentativas anônimas de consulta e alteração, e funcionamento em dispositivo móvel.

A aceitação funcional cabe ao responsável humano; testes técnicos isolados não equivalem à homologação.
