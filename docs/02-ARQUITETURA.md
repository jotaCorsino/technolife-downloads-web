# 02 — Arquitetura inicial

> Documento de planejamento. Nenhuma integração com a hospedagem ou com o HESK foi testada neste projeto.

## Visão em camadas

```text
Técnico autorizado (navegador)
          |
     HTML / CSS / JS
          |
    Requisições HTTPS
          |
    API pequena em PHP
      |         |
  Autorização  Persistência
    STAFF       compartilhada
          |
  Resposta de catálogo
```

Os executáveis ficam fora desta arquitetura: permanecem hospedados pelo serviço de downloads já existente. O painel mantém apenas referências (URLs), sem copiar, servir ou enviar arquivos.

## Frontend

- HTML semântico, CSS responsivo e JavaScript puro.
- Tela principal com busca, lista e ações de copiar, adicionar, editar e excluir.
- Formulário pequeno; estados de carregamento, lista vazia e erro.
- Sem frameworks nem interface administrativa adicional.
- Dados obtidos exclusivamente do backend autorizado; não usar `localStorage` como persistência compartilhada.

## Backend

Uma API PHP pequena deverá oferecer operações de **listagem, inclusão, edição e exclusão**. Os formatos finais dos endpoints serão definidos antes da implementação. A autorização deve ser aplicada **em todas as operações no servidor**, inclusive na leitura.

Os registros conterão `id`, `title`, `url` e, se necessário, `created_at` / `updated_at` gerados pelo servidor. Validar tamanho de campos, tratar Unicode e aceitar URLs HTTPS válidas; outras necessidades deverão ser explicitamente justificadas.

## Persistência — decisão pendente de validação

**Candidato preferencial:** SQLite por meio de PDO no PHP, caso a extensão esteja disponível e o ambiente suporte o arquivo de dados com permissões adequadas. O arquivo de banco deve residir **fora da área servida publicamente**.

Se SQLite não for viável, avaliar armazenamento em arquivo JSON com `flock`, escrita atômica e permissões restritas, ou outra solução mínima compatível. Não escolher por conveniência sem testar persistência e atualizações concorrentes.

Não versionar dados de produção.

## Autenticação e autorização

A preferência é aproveitar a sessão **STAFF** do HESK sem criar contas/senhas próprias. Essa integração **ainda não está comprovada**.

Antes de implementar o catálogo real, executar prova técnica que confirme:

1. Como o HESK identifica uma sessão de técnico válida **do lado servidor**.
2. Como o PHP do painel consegue verificar autenticação e autorização sem confiar em parâmetros ou cookies fornecidos isoladamente pelo navegador.
3. Como sessões expiradas, logout e usuários não autorizados são tratados.
4. Como impedir exposição anônima de HTML administrativo, conteúdo sensível e endpoints de API.
5. Como proteger operações de escrita contra CSRF, inclusive com sessão compartilhada.

Se não houver integração segura suportada pelo ambiente, registrar alternativas para decisão humana. **Não considerar acesso restrito resolvido com redirecionamento no JavaScript, ocultação de botões ou simples presença de cookie.**

## Segurança mínima

- HTTPS para o painel e para links admitidos por padrão.
- Validação de entradas no backend; URLs de esquemas perigosos (`javascript:`, `data:`, `file:`) rejeitadas.
- Saída de títulos tratada como texto, sem renderizar HTML arbitrário (proteção contra XSS).
- Proteção CSRF para ações que alteram estado; consultas e operações com métodos adequados.
- SQLite com statements preparados, se adotado.
- Erros sem dados de sessão, credenciais, caminhos internos ou stack traces públicos.
- Arquivos de dados/configuração e segredos fora do diretório publicamente servido e excluídos do Git.
- Testes negativos de usuário anônimo e sessão inválida antes de homologar.

## Implantação

Hospedagem PHP/cPanel já disponível, sujeita à verificação de versão PHP, extensões, permissões, roteamento e mecanismo de sessão. Endereço definitivo do painel, organização exata dos diretórios e procedimento de implantação serão definidos após a prova de ambiente.

Este repositório é público: usar exemplos fictícios, variáveis e referências genéricas. Não publicar configurações operacionais sensíveis nem o catálogo real.
