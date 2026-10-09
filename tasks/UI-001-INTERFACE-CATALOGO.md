# UI-001 — Interface web da Central de Links

**Estado:** AUTORIZADA em 09/10/2026 — implementação ainda não iniciada.
**Pré-requisito:** SCAN-001 homologada e integrada à `main` por meio do PR #1.
**Agente implementador:** Codex, na working copy local `technolife-downloads-web`.
**Branch prevista:** `feat/UI-001-interface-catalogo`.

## Objetivo

Construir **uma página única, simples, leve e responsiva** para a equipe Technolife localizar arquivos hospedados em `/downloads/`, pesquisar por nome e **copiar links HTTPS diretos** ou abri-los no navegador. A listagem será gerada **automaticamente pelo leitor PHP da SCAN-001**, sem cadastro manual.

Esta é a **interface do produto**. Não há banco de dados, autenticação, formulário administrativo, gerenciador de arquivos nem upload.

## Estado e base de trabalho

1. Consultar `README.md`, `docs/01-ESCOPO.md`, `docs/02-ARQUITETURA.md`, `docs/03-ROADMAP.md`, `docs/04-GOVERNANCA.md` e esta tarefa.
2. Confirmar Git limpo, `origin` correto e `main` sincronizada após o merge do PR #1. Não sobrescrever trabalhos locais.
3. Criar a branch `feat/UI-001-interface-catalogo` a partir da `main` atualizada.
4. Se o sandbox do Codex bloquear o `.git` como `tmpfs` somente leitura, diagnosticar a montagem e solicitar execução autorizada no **diretório original**; não criar pasta alternativa como primeiro recurso. Ver [registro de recorrência ÓRBITA](https://github.com/jotaCorsino/orbita-development-model/blob/main/troubleshooting/001-BOOTSTRAP-GIT-CODEX-SANDBOX-SOMENTE-LEITURA.md).

## Escopo incluído

### Página pública de catálogo

- Uma página inicial acessível por PHP (`index.php` ou equivalente, mantida tão simples quanto possível).
- Identidade visual inspirada nos sistemas Technolife anteriores: limpa, profissional, minimalista, espaçamento consistente e uso discreto de cores; evitar excesso de cartões, ícones, efeitos e dependências.
- Cabeçalho com nome **Central de Links** e descrição de uma linha.
- Campo de pesquisa em destaque, com filtragem instantânea e **sem diferenciação entre maiúsculas/minúsculas** (preferencialmente normalizar também acentos, se puder ser implementado com JS nativo).
- Contagem do total e/ou dos resultados visíveis.
- Lista de arquivos com **título normalizado**, indicação discreta da **extensão** e ações:
  - **Copiar link**: copia exatamente o URL HTTPS fornecido pelo leitor, com feedback de sucesso/falha;
  - **Abrir/Baixar**: link tradicional (`<a>`) apontando diretamente para a URL HTTPS; deixar o navegador/servidor decidir se baixa ou abre.
- Layout de desktop e celular, teclado e foco visível.
- Estados claros: pasta vazia, pesquisa sem resultados e erro de leitura/configuração.

### Integração com o scanner

- Reutilizar diretamente `src/DownloadScanner.php` da SCAN-001, sem duplicar a lógica.
- Configurar o **caminho do diretório no filesystem** e a **base HTTPS** exclusivamente por variáveis confiáveis do ambiente do PHP (por exemplo, `TECHNOLIFE_DOWNLOADS_DIR` e `TECHNOLIFE_DOWNLOADS_BASE_URL`), ou um mecanismo igualmente simples e seguro configurado no servidor. Não aceitar diretório, host ou URL-base de query string, formulários, headers ou cookies.
- **Sem configuração:** falhar de forma controlada e exibir mensagem genérica; não procurar automaticamente caminhos reais nem publicar uma lista acidental.
- Construir a apresentação a partir do resultado seguro da SCAN-001. Exibir campos de arquivos como texto, usando `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` ou métodos DOM seguros; proteger atributos HTML e URLs.
- Não habilitar listagem por diretório Apache/LiteSpeed; leitura ocorre via PHP.
- Lista atualizada quando a página é recarregada, sem necessidade de temporizadores/polling.
- Evitar renderizar nomes de arquivos reais de clientes ou divulgar paths físicos do servidor.

### Testes e experiência de desenvolvimento

- Preservar os testes existentes `tests/DownloadScannerTest.php`.
- Preparar uma forma documentada e **local** de executar a página com uma pasta **fictícia** de arquivos e URL-base HTTPS de exemplo, sem exigir acesso ao servidor cPanel.
- Testar o funcionamento básico em HTTP local (`localhost`) e instruir que em produção o acesso será HTTPS, especialmente para Clipboard API.
- Criar testes automatizados apropriados (PHP/JS nativo onde viável) e/ou um roteiro manual reproduzível para os casos de UI, sem adicionar frameworks ou ferramenta de build.
- Atualizar `README.md` com instruções locais sucintas, sem quebrar nem deslocar o quadro de status no topo. Atualizar `docs/03-ROADMAP.md` junto ao README conforme os estados do trabalho.

## Fora do escopo

- Cadastro, edição ou exclusão de links; upload ou manutenção de arquivos.
- Login, autenticação HESK, banco de dados, JSON persistido, cookies de usuário.
- Categorias, tags, dashboards, estatísticas e encurtador.
- Deploy, publicar URL de produção, configuração real do cPanel, inspeção do inventário real de downloads.
- Mudar o scanner de forma ampla; se detectar problema, relatar e propor correção restrita e justificada.
- Iniciar QA-001 ou DEP-001 automaticamente após implementar.

## Segurança e operação

**A página será pública caso seja implantada sem login**. Esta etapa autoriza implementar/testar a interface localmente, **não autoriza deploy público** nem divulgação do inventário real. Antes da publicação, o responsável verificará que a pasta contém somente arquivos e nomes publicáveis (QA/DEP).

- Nenhum segredo, credencial, caminho absoluto privado, arquivo de cliente ou inventário real no Git público.
- O PHP não fornece métodos administrativos; o formulário de busca não envia consultas a outros serviços.
- O uso de `navigator.clipboard.writeText` deve tratar rejeição/indisponibilidade com mensagem clara, sem alegar sucesso falso.
- Evitar `innerHTML` com dados não confiáveis. Não aceitar URLs ou atributos controlados pelo visitante.
- Links externos podem usar `target="_blank"` com `rel="noopener noreferrer"`, ou abrir na mesma aba, conforme decisão de UX documentada.
- Evitar dependências remotas (fontes/CDN/scripts) se não forem necessárias.

## Critérios de aceite

- [ ] A página abre localmente, consumindo dados obtidos **da SCAN-001** com fixtures.
- [ ] Títulos, extensões e URLs da fixture são apresentados corretamente; nenhum `.htaccess`, dotfile, subpasta ou symlink aparece.
- [ ] Busca filtra imediatamente sem recarregar a página; estado de nenhum resultado funciona.
- [ ] Copiar link copia o HTTPS integral em ambiente compatível; falha do clipboard gera feedback verdadeiro.
- [ ] Abrir/Baixar usa a URL direta do arquivo, sem endpoint de redirecionamento desnecessário.
- [ ] Pasta vazia e configurações/leituras inválidas produzem mensagens compreensíveis sem revelar paths internos.
- [ ] A página é utilizável no celular e por teclado.
- [ ] Nenhuma funcionalidade de administração, login ou persistência foi introduzida.
- [ ] Scanner e seus testes originais continuam funcionando; testes da interface documentados e executados.
- [ ] Código e README estão revisáveis num PR; nenhuma alteração funcional na `main` sem homologação.

## Evidências exigidas

- Arquivos alterados, descrição sucinta do layout e do fluxo de interação.
- Instruções e evidência de execução local com pasta fictícia.
- Exemplo de listagem normalizada, busca, cópia e tratamento de erro.
- Resultados dos testes `php -l`, `php tests/DownloadScannerTest.php` e testes adicionais.
- Branch, commits, número do PR, estado da working tree.
- Eventuais decisões sobre CSS, scripts e configuração do ambiente; riscos e pendências para QA/DEP.

**Gate:** criar ou atualizar um único PR da UI-001 e parar em **AGUARDANDO_HOMOLOGACAO**. Não realizar merge, deploy ou iniciar QA-001.
