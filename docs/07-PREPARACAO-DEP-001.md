# 07 — DEP-001, Fase A: pacote local para `/links/`

**Estado:** preparado no notebook em 09/10/2026; aguardando revisão do PR. **Nada foi enviado ao servidor da empresa.** A Fase B será feita manualmente pelo responsável após confirmar o inventário publicável e o ambiente do cPanel.

## Pacote produzido

Execute no repositório: `python3 deployment/build_package.py`. Os arquivos ficam em `dist/DEP-001/` (ignorado pelo Git):

| Arquivo | Onde extrair no Gerenciador de Arquivos | Conteúdo |
| --- | --- | --- |
| `DEP-001-public-links.zip` | **document root** confirmado do domínio `suporte.technolife.net.br` | Cria `links/index.php`, `links/styles.css`, `links/app.js` e `links/assets/` |
| `DEP-001-private-reader.zip` | Diretório **fora de todas as raízes web** da conta | Cria `technolife-links-private/DownloadScanner.php` e `config.php.example` |
| `SHA256SUMS` | Fica no notebook, para conferir os dois ZIPs | SHA-256 dos pacotes |
| `MANIFEST.txt` | Fica no notebook | Lista a separação e informa se o localizador está ativado |

Os ZIPs não contêm `config.php`, valores reais, arquivos de downloads, `tests/`, `docs/`, `.git/` ou dependências. O pacote padrão traz `PRIVATE_PARENT_LEVELS = 0`: a página **falha fechada** até que o local privado seja confirmado e configurado. Nunca extraia o ZIP privado dentro de `public_html`, do document root de outro domínio ou de `links/`.

## Instruções manuais no cPanel — após aprovar o pacote

1. No **Gerenciador de Arquivos**, confirme o **Document Root for** `suporte.technolife.net.br` e a pasta inicial da conta. A [documentação oficial do File Manager](https://docs.cpanel.net/cpanel/files/file-manager/110/) descreve as opções **Settings**, **Upload**, **Extract**, **Rename** e **Edit**. Não presuma que a raiz do domínio seja `public_html`.
2. Identifique um diretório da conta **fora de todas as raízes públicas**, inclusive `public_html` quando existir. Ali, envie `DEP-001-private-reader.zip` e use **Extract**. Confirme que `technolife-links-private/` não pode ser alcançada por nenhum domínio da conta. Apague o ZIP após a extração.
3. Dentro dessa pasta privada, renomeie `config.php.example` para `config.php` e edite **apenas no servidor**:

   ```php
   <?php
   return [
       'downloads_dir' => '/CAMINHO_ABSOLUTO_CONFIRMADO/downloads',
       'downloads_base_url' => 'https://suporte.technolife.net.br/downloads',
   ];
   ```

   O caminho acima é um **exemplo fictício**, não o caminho da empresa. Confirme no cPanel que ele aponta para a mesma pasta servida pela URL-base. O arquivo real nunca deve entrar no Git ou em pasta pública. Se o processo PHP web já receber **ambas** as variáveis `TECHNOLIFE_DOWNLOADS_DIR` e `TECHNOLIFE_DOWNLOADS_BASE_URL`, ele as usa no lugar de `config.php`; uma variável isolada faz a página falhar fechada.
4. Confirme PHP 8.2 ou superior, permissão de leitura da pasta de downloads e acesso do PHP ao leitor/configuração privados (inclusive `open_basedir`, se ativo). A configuração do terminal local **não** comprova a do PHP HTTP. Se essa leitura não for possível, interrompa a publicação.
5. Antes de ativar a página, revise **todos os arquivos atuais e futuros** de `/downloads/` com o responsável. Sem login, seus nomes e URLs aparecerão publicamente. Material não publicável bloqueia esta implantação.
6. No document root **confirmado** de `suporte.technolife.net.br`, faça backup de uma pasta `links/` preexistente, caso exista. Envie `DEP-001-public-links.zip` e use **Extract**. O resultado deve ser `links/index.php` diretamente, **sem** `links/public/` e sem alterar HESK ou `downloads/`. Apague o ZIP público após extrair: ele contém código fonte.
7. Configure **somente o localizador do leitor**. Abra `links/index.php` no File Manager e substitua `const PRIVATE_PARENT_LEVELS = 0;` pelo número de pastas que se sobe a partir de `links/` até o diretório que contém `technolife-links-private/`. Exemplos fictícios: `.../public_html/links/` → `2`; `.../public_html/suporte/links/` → `3`. Use **2 ou mais** e confira o resultado no File Manager. Se o layout não corresponder ou a pasta calculada estiver sob qualquer webroot, mantenha `0` e não publique. Alternativamente, um administrador pode fornecer `TECHNOLIFE_LINKS_PRIVATE_DIR` ao **PHP web** com o caminho privado absoluto; teste essa variável no contexto HTTP. O código rejeita diretórios dentro do document root ativo e sob `public_html`, mas isso **não substitui** a conferência das demais raízes web.
8. Depois dos gates, verifique `https://suporte.technolife.net.br/links/` e também `/links`; lista, pesquisa, cópia e abertura de um arquivo permitido; assets, celular, mensagens de erro e URL HTTPS. Confirme que leitor, `config.php`, ZIPs e metadados Git não são acessíveis por nenhuma URL e que HESK e `/downloads/` continuam íntegros. Registre o resultado sem divulgar caminhos privados.

**Rollback:** mantenha o backup da pasta `links/` anterior. Se o smoke test falhar, volte `PRIVATE_PARENT_LEVELS` a `0` para fechar a listagem, retire a nova pasta `links/` e restaure o backup, se houver. Preserve HESK e `/downloads/`; remova a configuração privada criada para esta implantação somente após confirmar que nenhum outro processo a usa.

## Evidência da Fase A no notebook

- `main` sincronizada após o PR #3; branch `deploy/DEP-001-preparacao-links`.
- Pacotes gerados por lista explícita de arquivos; público e privado em ZIPs separados, sem configuração preenchida. `SHA256SUMS` permite conferir o conteúdo entregue.
- Suítes SCAN-001 e UI-001 preservadas: 9 grupos do scanner, teste PHP da interface e 2 testes JavaScript aprovados.
- Teste com layout fictício `webroot/links/` e `technolife-links-private/` fora da webroot: padrão sem configuração exibiu erro genérico; após configurar a cópia temporária com nível `2` e dados fictícios, listou 2 arquivos, gerou URL codificada, pesquisou e copiou o HTTPS exato.
- Servidor PHP local: `/links/`, CSS e logo HTTP 200; `/links` exibiu a página; tentativas de ler `/src/DownloadScanner.php` e `/technolife-links-private/DownloadScanner.php` pela web retornaram 404.
- **Não validado aqui:** caminhos, permissões, versão/handler PHP, comportamento HTTP e inventário real do cPanel. Nenhum acesso ao servidor da empresa foi realizado.

**Gate:** PR da Fase A para revisão. O pacote local não autoriza a Fase B nem confirma a publicabilidade do catálogo.
