// Kanban board: drag cards between columns (or use each card's "Move to" menu).
// The page is already rendered by Laravel; this script only moves cards around
// and tells the server about it with fetch().

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const message = document.getElementById('board-message');
const columns = [...document.querySelectorAll('[data-column]')];

let dragged = null;

function showError(text) {
    message.textContent = text;
    message.classList.remove('hidden');
}

/** Refresh one column's card count and its "Nothing here" placeholder. */
function updateColumn(column) {
    const count = column.querySelectorAll('[data-card]').length;
    column.querySelector('[data-count]').textContent = count;
    column.querySelector('[data-empty]').classList.toggle('hidden', count > 0);
}

/** Put a card into a column and keep that card's dropdown in sync. */
function place(card, column) {
    column.querySelector('[data-dropzone]').prepend(card);
    card.querySelector('[data-move]').value = column.dataset.status;
}

async function moveCard(card, targetColumn) {
    const originColumn = card.closest('[data-column]');
    if (originColumn === targetColumn) return;

    message.classList.add('hidden');

    // Optimistic update: move it now, undo if the server says no.
    place(card, targetColumn);
    updateColumn(originColumn);
    updateColumn(targetColumn);

    try {
        const response = await fetch(card.dataset.url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ status: targetColumn.dataset.status }),
        });

        if (!response.ok) throw new Error(`Server replied ${response.status}`);
    } catch (error) {
        place(card, originColumn);
        updateColumn(originColumn);
        updateColumn(targetColumn);
        showError('Could not move that card. Please refresh the page and try again.');
    }
}

// Drag and drop (mouse). Uses the browser's built-in drag events.
document.addEventListener('dragstart', (event) => {
    const card = event.target.closest?.('[data-card]');
    if (!card) return;
    dragged = card;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', card.dataset.url); // Firefox needs some data to start a drag
    card.classList.add('opacity-50');
});

document.addEventListener('dragend', () => {
    dragged?.classList.remove('opacity-50');
    dragged = null;
    columns.forEach((column) => column.classList.remove('ring-2', 'ring-indigo-400'));
});

columns.forEach((column) => {
    column.addEventListener('dragover', (event) => {
        if (!dragged) return;
        event.preventDefault(); // without this the browser refuses the drop
        column.classList.add('ring-2', 'ring-indigo-400');
    });

    column.addEventListener('dragleave', (event) => {
        if (!column.contains(event.relatedTarget)) {
            column.classList.remove('ring-2', 'ring-indigo-400');
        }
    });

    column.addEventListener('drop', (event) => {
        if (!dragged) return;
        event.preventDefault();
        moveCard(dragged, column);
    });
});

// "Move to" dropdown (touch screens and keyboard).
document.addEventListener('change', (event) => {
    const select = event.target.closest?.('[data-move]');
    if (!select) return;
    const card = select.closest('[data-card]');
    const target = columns.find((column) => column.dataset.status === select.value);
    if (card && target) moveCard(card, target);
});
