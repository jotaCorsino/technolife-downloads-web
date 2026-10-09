# Technolife — Central de Links

Aplicação web interna para localizar, cadastrar e compartilhar links diretos de download utilizados pela equipe de suporte técnico da Technolife.

## Problema

Os instaladores e arquivos permanecem hospedados no ambiente da empresa, mas os técnicos precisam de um catálogo único para encontrar e copiar seus endereços sem procurar manualmente arquivos ou acessar o painel da hospedagem.

## Solução proposta

Um painel enxuto, com pesquisa instantânea e operações de **copiar**, **cadastrar**, **editar** e **excluir** links. Cada registro exige somente **título** e **URL**. As alterações devem ser compartilhadas entre os técnicos autorizados.

A aplicação **não armazena executáveis, não faz upload e não substitui a hospedagem dos arquivos**: organiza apenas os respectivos endereços.

## MVP

| Recurso | Comportamento esperado |
| --- | --- |
| Catálogo | Listar os programas cadastrados |
| Busca | Filtrar imediatamente pelo título |
| Copiar link | Copiar URL com um clique e indicar sucesso/erro |
| Novo link | Informar título e URL, validar e salvar |
| Editar/excluir | Manter os registros atualizados; confirmar exclusões |
| Dados compartilhados | Persistir os registros para toda a equipe |
| Acesso restrito | Somente técnicos autorizados podem consultar e alterar |

## Diretrizes técnicas

- **Interface:** HTML, CSS e JavaScript puro, responsivo e sem frameworks desnecessários.
- **Servidor:** PHP compatível com a hospedagem existente, sujeito à validação no ambiente real.
- **Persistência:** armazenamento compartilhado simples; SQLite é candidato, condicionado à disponibilidade da extensão e ao ambiente; alternativa será avaliada.
- **Autenticação:** preferência por reutilizar a sessão de STAFF do HESK, **dependendo de prova de integração segura**. Não presumir que compartilhar domínio ou cookie garante autorização.
- **Hospedagem dos arquivos:** separada do catálogo; o sistema manipula apenas links.

**Estado atual:** fundação documental; nenhuma funcionalidade implementada ou integração validada.

## Documentação

- [Escopo e critérios do MVP](docs/01-ESCOPO.md)
- [Arquitetura inicial e segurança](docs/02-ARQUITETURA.md)
- [Roadmap e estado do projeto](docs/03-ROADMAP.md)
- [Regras de desenvolvimento e homologação](docs/04-GOVERNANCA.md)
- [Decisões, premissas e riscos](docs/05-DECISOES-E-RISCOS.md)
- [Primeira tarefa: bootstrap local](tasks/BOOT-001-BOOTSTRAP-LOCAL.md)

## Forma de trabalho

O projeto usa o [Método ÓRBITA](https://github.com/jotaCorsino/orbita-development-model): **Planejar → Executar → Evidenciar → Homologar**.

O responsável humano decide e homologa. O ChatGPT organiza planejamento e documentação. O Codex executa tarefas autorizadas na cópia local, valida e apresenta evidências. O GitHub é a referência persistente do projeto.

**Regra de partida:** fundação documental remota → bootstrap local seguro → primeira funcionalidade autorizada.

## Segurança e publicação

Este repositório é **público**. Não registrar nele segredos, credenciais, arquivos reais de clientes, sessões, banco de produção, caminhos internos privados ou configurações confidenciais. Somente um backend com autorização efetiva poderá expor o catálogo e as operações administrativas no ambiente de produção.

## Situação em 09/10/2026

Fundação inicial de planejamento criada. Próximo gate: conectar a pasta local **technolife-downloads-web** ao remoto e confirmar a sincronização, sem implementar funcionalidades.
