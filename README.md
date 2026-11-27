# Technolife — Central de Links

Catálogo automático de downloads, somente leitura, com busca e cópia de URLs. PHP, HTML/CSS e JavaScript nativo; sem login, banco ou cadastro de arquivos.

_Atualizado em 27/11/2026 · Adequação documental HUB-EVO-001/08 em revisão._

## Situação atual

| Indicador | Situação |
| --- | --- |
| **Primeira versão** | MVP e DEP-001 homologados em 09/10/2026; operação de listagem, cópia e download confirmada pelo responsável |
| **Agora** | Governança e painel Modelo B preparados; aplicação e implantação existentes preservadas |
| **Quanto falta** | Nenhuma pendência do MVP registrada. Adequação documental aguarda aceite; evolução visual futura por gate próprio |
| **Próximo passo** | Homologar esta adequação; manter operação e tratar somente demanda autorizada |

## Pendências

| Prioridade | O que falta | Próxima ação |
| --- | --- | --- |
| Documental | Aceite/integração da governança | Revisar esta PR e [AGENTS](AGENTS.md) |
| Futura | Avaliar SIS-EVO-001 quando referência homologada | Não impor login, sidebar, banco ou refatoração |
| Operacional | Exposição pública de novos arquivos | Confirmar autorização de divulgação; não tratar URL obscura como controle de acesso |

## Evolução

| Marco | Resultado | Evidência |
| --- | --- | --- |
| INIT-001 / BOOT-001 | Fundação e ambiente vinculados | [Roadmap](docs/03-ROADMAP.md) |
| SCAN-001 / UI-001 | Leitor, busca e cópia homologados | [Leitura](tasks/SCAN-001-LEITOR-DOWNLOADS.md) · [Interface](tasks/UI-001-INTERFACE-CATALOGO.md) |
| QA-001 | Segurança e validações funcionais aprovadas | [QA](docs/06-RELATORIO-QA.md) |
| DEP-001 | Implantação e aceite operacional em 09/10, HTTP 200/403/404 nos escopos registrados, HESK preservado | [Preparação](docs/07-PREPARACAO-DEP-001.md) · [Roadmap](docs/03-ROADMAP.md) |

## Onde consultar

[Escopo](docs/01-ESCOPO.md) · [Arquitetura](docs/02-ARQUITETURA.md) · [Governança](docs/04-GOVERNANCA.md) · [Decisões](docs/05-DECISOES-E-RISCOS.md) · [QA](docs/06-RELATORIO-QA.md) · [Execução local e comandos aprovados no README anterior](https://github.com/jotaCorsino/technolife-downloads-web/blob/cfe912148435ec1bcffefc19b0559092db5bff9c/README.md#execução-local-com-arquivos-fictícios) · [AGENTS](AGENTS.md).

## Limites

Catálogo público apenas de arquivos autorizados: ignorar dotfiles, subpastas e symlinks, preservar escape/URL HTTPS e erros sem dados sensíveis. Sem autenticação própria, edição, upload, CRUD ou integração STAFF. A prova operacional de 09/10 é histórica, não nova inspeção da produção. Sem deploy ou adoção automática do kit nesta entrega.
