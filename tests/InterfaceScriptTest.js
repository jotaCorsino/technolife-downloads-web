const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const script = fs.readFileSync(path.join(__dirname, '../public/app.js'), 'utf8');

function setup(searchTexts, urls = []) {
  const search = { value: '', listeners: {}, addEventListener(type, callback) { this.listeners[type] = callback; } };
  const count = { textContent: '' };
  const noResults = { hidden: true };
  const rows = searchTexts.map((text) => ({ dataset: { search: text }, hidden: false }));
  const buttons = urls.map((url) => {
    const feedback = { textContent: '', dataset: {} };
    const button = {
      dataset: { copyUrl: url },
      listeners: {},
      addEventListener(type, callback) { this.listeners[type] = callback; },
      parentElement: { querySelector() { return feedback; } },
    };
    return { button, feedback };
  });
  const navigator = { clipboard: { writeText: async () => {} } };
  const document = {
    querySelector(selector) {
      return { '#catalog-search': search, '#result-count': count, '#no-results': noResults }[selector];
    },
    querySelectorAll(selector) {
      return selector === '[data-file-row]' ? rows : buttons.map(({ button }) => button);
    },
  };
  vm.runInNewContext(script, { document, navigator });
  return { search, count, noResults, rows, buttons, navigator };
}

test('pesquisa imediata ignora caixa e acentos e mostra nenhum resultado', () => {
  const ui = setup(['Olá Mundo #1?.tar.gz', 'Technolife RustDesk Windows']);
  ui.search.value = 'OLA';
  ui.search.listeners.input();
  assert.deepEqual(ui.rows.map((row) => row.hidden), [false, true]);
  assert.equal(ui.count.textContent, '1 de 2 arquivos');
  assert.equal(ui.noResults.hidden, true);

  ui.search.value = 'inexistente';
  ui.search.listeners.input();
  assert.deepEqual(ui.rows.map((row) => row.hidden), [true, true]);
  assert.equal(ui.noResults.hidden, false);
  assert.equal(ui.count.textContent, '0 de 2 arquivos');

  ui.search.value = '';
  ui.search.listeners.input();
  assert.deepEqual(ui.rows.map((row) => row.hidden), [false, false]);
  assert.equal(ui.noResults.hidden, true);
});

test('cópia usa URL integral e informa sucesso ou falha real', async () => {
  const url = 'https://example.invalid/downloads/Ol%C3%A1%20Mundo%20%231%3F.tar.gz';
  const ui = setup(['Olá Mundo'], [url]);
  let copied = null;
  ui.navigator.clipboard.writeText = async (value) => { copied = value; };
  await ui.buttons[0].button.listeners.click();
  assert.equal(copied, url);
  assert.equal(ui.buttons[0].feedback.textContent, 'Link copiado.');
  assert.equal(ui.buttons[0].feedback.dataset.state, 'success');

  ui.navigator.clipboard.writeText = async () => { throw new Error('Permissão negada'); };
  await ui.buttons[0].button.listeners.click();
  assert.match(ui.buttons[0].feedback.textContent, /Não foi possível copiar/);
  assert.equal(ui.buttons[0].feedback.dataset.state, 'error');

  ui.navigator.clipboard = undefined;
  await ui.buttons[0].button.listeners.click();
  assert.match(ui.buttons[0].feedback.textContent, /Não foi possível copiar/);
});
