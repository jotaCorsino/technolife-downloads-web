# 05 — Decisões, premissas e riscos

## Decisões de produto (revisadas em 09/10/2026)

| ID | Decisão | Estado |
| --- | --- | --- |
| DEC-001 | Catálogo de links diretos para arquivos já hospedados em `/downloads/` | Confirmada |
| DEC-002 | Sem cadastro: arquivos existentes na pasta são a fonte única de dados | Confirmada |
| DEC-003 | Somente leitura: abrir/baixar URL e copiar link | Confirmada |
| DEC-004 | Pesquisa instantânea e normalização dos nomes dos arquivos | Confirmada |
| DEC-005 | Sem login, integração HESK ou controle de acesso próprio | Confirmada |
| DEC-006 | Sem banco de dados, arquivos JSON de cadastro ou APIs de escrita | Confirmada |
| DEC-007 | HTML/CSS/JavaScript puro + PHP mínimo para leitura local do diretório | Proposta técnica |
| DEC-008 | Método ÓRBITA, com implementação incremental e homologação humana | Confirmada |
| DEC-009 | Na primeira versão, só arquivos regulares diretamente na pasta, excluindo dotfiles, diretórios e symlinks | Diretriz de segurança |

## Mudança de escopo

A primeira proposta previa CRUD manual, persistência compartilhada e eventual autenticação STAFF. O responsável pelo projeto substituiu esse modelo por uma página de listagem automática, pública e sem operações administrativas, porque o objetivo é somente localizar e compartilhar endereços.

**As antigas tarefas de CRUD, banco de dados e autenticação HESK estão canceladas e não devem ser implementadas.**

## Validações antes do deploy

**PEND-001 — Publicação:** a ausência de login significa que os títulos, extensões, nomes reais e URLs da pasta serão publicamente enumeráveis. Confirmar que esse inventário pode ser divulgado. Se existir conteúdo restrito na pasta, a versão pública deve ser bloqueada ou a fonte de listagem revista antes do deploy.

**PEND-002 — Localização física:** confirmar caminho real do diretório `/downloads/` e permissões de leitura pelo processo PHP sem divulgar paths privados.

**PEND-003 — URL base:** confirmar a base HTTPS dos downloads e testar nomes com espaços, acentos e caracteres reservados.

**PEND-004 — UI:** verificar detalhes da identidade visual usada em outros projetos Technolife antes da homologação.

## Riscos e controles

| Risco | Tratamento esperado |
| --- | --- |
| Catálogo público revela todo o inventário da pasta | Revisão humana da pasta e autorização explícita de publicação |
| `.htaccess`, dotfiles, links simbólicos ou pastas aparecem no catálogo | Filtrar nomes iniciados em ponto, não seguir symlinks, aceitar só arquivos regulares |
| Nome malicioso causa XSS | Escapar HTML / renderizar nomes como texto |
| URL incorreta por caracteres especiais | Codificar nome como segmento de URL e testar casos reais |
| Entrada fornecida por visitante altera pasta consultada | Diretório fixo do lado servidor, sem parâmetros de caminho |
| Erro do servidor expõe filesystem | Mensagem genérica e logging seguro |
| Novos arquivos sensíveis surgem na pasta após deploy | Procedimento operacional para manter apenas arquivos publicáveis na origem indexada |

**Não há credenciais, armazenamento compartilhado nem lógica administrativa a desenvolver.** O objetivo da arquitetura é minimizar superfície de risco mantendo o produto funcional.
