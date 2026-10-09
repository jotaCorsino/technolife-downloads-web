# 05 — Decisões, premissas e riscos

## Decisões já definidas pelo escopo

| ID | Decisão | Situação |
| --- | --- | --- |
| DEC-001 | Produto é catálogo de **links**, não gerenciador/upload de executáveis | Confirmada |
| DEC-002 | Interface minimalista, responsiva, com busca e copiar em um clique | Confirmada |
| DEC-003 | Técnicos cadastram, editam e excluem títulos/URLs | Confirmada |
| DEC-004 | Alterações precisam ser compartilhadas e persistentes | Confirmada |
| DEC-005 | Priorizar HTML, CSS e JavaScript puro; PHP é opção natural no cPanel | Diretriz confirmada; ambiente a validar |
| DEC-006 | Preferir autenticação STAFF do HESK sem login novo | Preferência; viabilidade pendente |
| DEC-007 | SQLite via PDO é candidato inicial a persistência | Proposta técnica; pendente de validação |
| DEC-008 | Trabalhar com ChatGPT/Codex/GitHub pelo Método ÓRBITA | Diretriz de processo |

## Questões técnicas ainda em aberto

**PEND-001 — Autenticação:** confirmar um método efetivamente seguro para verificar STAFF do HESK no PHP do painel, inclusive logout, sessões expiradas e CSRF. A simples presença de cookies do HESK não basta.

**PEND-002 — Persistência:** testar suporte real a SQLite/PDO, armazenamento fora do webroot, permissões e concorrência. Se inviável, comparar alternativa simples e segura.

**PEND-003 — Hospedagem:** estabelecer caminho definitivo do painel e o procedimento de publicação após validação do ambiente.

**PEND-004 — Identidade visual:** observar componentes visuais efetivamente utilizados nos aplicativos internos antes de fixar cores, logotipo e detalhes de UI.

**PEND-005 — Visibilidade:** repositório atualmente público; confirmar quais partes da implementação e da documentação podem ser publicadas sem revelar informações operacionais.

## Riscos relevantes e tratamento esperado

| Risco | Impacto | Prevenção / gate |
| --- | --- | --- |
| API acessível sem STAFF | Exposição ou alteração não autorizada do catálogo | Prova de autenticação no servidor + testes negativos antes do CRUD |
| Repositório público com detalhes internos | Exposição de infraestrutura/segredos | Documentação sanitizada; não versionar configuração real |
| Banco/JSON em diretório público | Vazamento de links e metadados | Dados fora do webroot; verificar permissões |
| Escritas simultâneas | Perda ou corrupção de registros | Transações SQLite ou estratégia de locking atômico |
| URL ou título malicioso | XSS, phishing, esquemas perigosos | Validação de URL e renderização segura de texto |
| Falsa sensação de proteção por UI | Admin apenas oculto no navegador | Autorização em todos os endpoints, independente de UI |
| Integração frágil com sessão HESK | Acesso indevido ou quebra com atualizações | POC isolada e testes de expiração/logout |

## Critério para decidir pendências

Registrar a decisão e sua evidência técnica antes de implementação dependente dela. O responsável humano aprova concessões de segurança ou mudanças de escopo. Problemas críticos não devem ser ignorados por estarem fora da tarefa corrente.
