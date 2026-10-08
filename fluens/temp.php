<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Todo List</title>
<style>
  :root {
    --bg: #f4f5f7;
    --card: #ffffff;
    --text: #1f2430;
    --muted: #8a90a2;
    --accent: #5b6cff;
    --border: #e6e8ef;
  }

  @media (prefers-color-scheme: dark) {
    :root {
      --bg: #14161c;
      --card: #1d2027;
      --text: #e9ecf3;
      --muted: #868da0;
      --accent: #7c8aff;
      --border: #2c303a;
    }
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 24px;
    background: var(--bg);
    color: var(--text);
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
  }

  .app {
    width: 100%;
    max-width: 460px;
    background: var(--card);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, .12);
  }

  h1 {
    margin: 0 0 18px;
    font-size: 1.4rem;
    letter-spacing: -.02em;
  }

  /* --- Form --- */
  #todo-form {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }

  #todo-input {
    flex: 1;
    min-width: 0;
    padding: 12px 14px;
    font: inherit;
    color: inherit;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 10px;
    outline: none;
    transition: border-color .15s;
  }

  #todo-input:focus { border-color: var(--accent); }
  #todo-input::placeholder { color: var(--muted); }

  #todo-form button {
    padding: 12px 18px;
    font: inherit;
    font-weight: 600;
    color: #fff;
    background: var(--accent);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
  }

  #todo-form button:hover { opacity: .9; }
  #todo-form button:active { transform: scale(.97); }

  /* --- List --- */
  #todo-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .todo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 8px;
    border-radius: 10px;
    transition: background .15s;
  }

  .todo:hover { background: rgba(128, 128, 128, .09); }

  .todo input[type="checkbox"] {
    width: 19px;
    height: 19px;
    flex-shrink: 0;
    accent-color: var(--accent);
    cursor: pointer;
  }

  .todo .text {
    flex: 1;
    word-break: break-word;
    line-height: 1.4;
    transition: color .2s;
  }

  .todo.done .text {
    color: var(--muted);
    text-decoration: line-through;
  }

  .todo .delete {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    font-size: 15px;
    line-height: 1;
    color: var(--muted);
    background: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    opacity: 0;
    transition: opacity .15s, color .15s, background .15s;
  }

  .todo:hover .delete,
  .todo .delete:focus-visible { opacity: 1; }

  .todo .delete:hover {
    color: #e5484d;
    background: rgba(229, 72, 77, .12);
  }

  .empty {
    padding: 24px 8px;
    text-align: center;
    color: var(--muted);
    font-size: .9rem;
  }

  /* --- Footer --- */
  .footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    font-size: .82rem;
    color: var(--muted);
  }

  .filters { display: flex; gap: 4px; }

  .filters button {
    padding: 5px 10px;
    font: inherit;
    font-size: .8rem;
    color: var(--muted);
    background: none;
    border: 1px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: color .15s, border-color .15s;
  }

  .filters button:hover { color: var(--text); }

  .filters button.active {
    color: var(--accent);
    border-color: currentColor;
  }

  #clear-completed {
    font: inherit;
    font-size: .8rem;
    color: var(--muted);
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px 4px;
  }

  #clear-completed:hover { color: #e5484d; }
</style>
</head>
<body>

<div class="app">
  <h1>Todo List</h1>

  <form id="todo-form" autocomplete="off">
    <input id="todo-input" type="text" placeholder="What needs to be done?" aria-label="New task">
    <button type="submit">Add</button>
  </form>

  <ul id="todo-list"></ul>

  <div class="footer">
    <span id="count">0 items left</span>

    <div class="filters">
      <button data-filter="all" class="active">All</button>
      <button data-filter="active">Active</button>
      <button data-filter="completed">Completed</button>
    </div>

    <button id="clear-completed" hidden>Clear completed</button>
  </div>
</div>

<script>
(() => {
  'use strict';

  const STORAGE_KEY = 'todo-list-v1';

  const form      = document.getElementById('todo-form');
  const input     = document.getElementById('todo-input');
  const list      = document.getElementById('todo-list');
  const countEl   = document.getElementById('count');
  const clearBtn  = document.getElementById('clear-completed');
  const filterBtns = document.querySelectorAll('[data-filter]');

  let todos  = load();
  let filter = 'all';

  /* ---------- Persistence ---------- */

  function load() {
    try {
      const data = JSON.parse(localStorage.getItem(STORAGE_KEY));
      return Array.isArray(data) ? data : [];
    } catch {
      return [];
    }
  }

  function save() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(todos));
  }

  /* ---------- Actions ---------- */

  function addTodo(text) {
    todos.push({
      id: crypto.randomUUID ? crypto.randomUUID() : String(Date.now() + Math.random()),
      text,
      done: false
    });
    save();
    render();
  }

  function toggleTodo(id) {
    const todo = todos.find(t => t.id === id);
    if (todo) {
      todo.done = !todo.done;
      save();
      render();
    }
  }

  function removeTodo(id) {
    todos = todos.filter(t => t.id !== id);
    save();
    render();
  }

  function clearCompleted() {
    todos = todos.filter(t => !t.done);
    save();
    render();
  }

  /* ---------- Render ---------- */

  function render() {
    list.innerHTML = '';

    const visible = todos.filter(t => {
      if (filter === 'active')    return !t.done;
      if (filter === 'completed') return t.done;
      return true;
    });

    if (visible.length === 0) {
      const li = document.createElement('li');
      li.className = 'empty';
      li.textContent = todos.length === 0
        ? 'Nothing here yet — add your first task!'
        : `No ${filter} tasks.`;
      list.append(li);
    } else {
      for (const todo of visible) {
        const li = document.createElement('li');
        li.className = 'todo' + (todo.done ? ' done' : '');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = todo.done;
        checkbox.setAttribute('aria-label', 'Mark as done');
        checkbox.addEventListener('change', () => toggleTodo(todo.id));

        const span = document.createElement('span');
        span.className = 'text';
        span.textContent = todo.text;   // textContent = safe from HTML injection

        const del = document.createElement('button');
        del.className = 'delete';
        del.type = 'button';
        del.textContent = '✕';
        del.setAttribute('aria-label', 'Delete task');
        del.addEventListener('click', () => removeTodo(todo.id));

        li.append(checkbox, span, del);
        list.append(li);
      }
    }

    const remaining = todos.filter(t => !t.done).length;
    countEl.textContent = `${remaining} item${remaining === 1 ? '' : 's'} left`;
    clearBtn.hidden = !todos.some(t => t.done);
  }

  /* ---------- Events ---------- */

  form.addEventListener('submit', e => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    addTodo(text);
    input.value = '';
    input.focus();
  });

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filter = btn.dataset.filter;
      filterBtns.forEach(b => b.classList.toggle('active', b === btn));
      render();
    });
  });

  clearBtn.addEventListener('click', clearCompleted);

  /* ---------- Init ---------- */

  render();
})();
</script>

</body>
</html>