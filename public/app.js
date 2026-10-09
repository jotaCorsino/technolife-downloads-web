(() => {
  const search = document.querySelector('#catalog-search');
  const rows = Array.from(document.querySelectorAll('[data-file-row]'));
  const count = document.querySelector('#result-count');
  const noResults = document.querySelector('#no-results');

  const normalize = (text) => text
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLocaleLowerCase('pt-BR');

  if (search && count && noResults) {
    const searchable = rows.map((row) => ({ row, text: normalize(row.dataset.search || '') }));

    search.addEventListener('input', () => {
      const query = normalize(search.value.trim());
      let visible = 0;

      searchable.forEach(({ row, text }) => {
        row.hidden = !text.includes(query);
        if (!row.hidden) visible += 1;
      });

      noResults.hidden = visible !== 0;
      count.textContent = query
        ? `${visible} de ${rows.length} arquivos`
        : `${rows.length} ${rows.length === 1 ? 'arquivo disponível' : 'arquivos disponíveis'}`;
    });
  }

  document.querySelectorAll('[data-copy-url]').forEach((button) => {
    button.addEventListener('click', async () => {
      const feedback = button.parentElement.querySelector('.copy-feedback');
      if (!feedback) return;

      try {
        if (!navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') {
          throw new Error('Clipboard indisponível');
        }
        await navigator.clipboard.writeText(button.dataset.copyUrl);
        feedback.textContent = 'Link copiado.';
        feedback.dataset.state = 'success';
      } catch (_error) {
        feedback.textContent = 'Não foi possível copiar. Use o link Abrir / baixar para acessar o arquivo.';
        feedback.dataset.state = 'error';
      }
    });
  });
})();
