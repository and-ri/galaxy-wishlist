import './bootstrap';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]').content;

async function postJson(url, body) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify(body),
    });

    return response.json();
}

function showImagePreview(src) {
    const container = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');

    if (!container || !preview) {
        return;
    }

    preview.src = src;
    container.classList.remove('hidden');
}

// Автозаповнення бажання з посилання на товар
function initUrlParser() {
    const urlInput = document.getElementById('wish-url');
    const button = document.getElementById('parse-url-btn');
    const status = document.getElementById('parse-status');

    if (!urlInput || !button) {
        return;
    }

    const label = button.querySelector('[data-label]');
    const defaultHint = status?.textContent;

    const setStatus = (text, tone = 'hint') => {
        if (!status) {
            return;
        }

        status.textContent = text;
        status.className = tone === 'error' ? 'field-error' : 'field-hint';
    };

    const fillIfEmpty = (id, value) => {
        const input = document.getElementById(id);

        if (input && value && !input.value) {
            input.value = value;
        }
    };

    button.addEventListener('click', async () => {
        const url = urlInput.value.trim();

        if (!url) {
            setStatus('Спершу вставте посилання на товар.', 'error');
            urlInput.focus();
            return;
        }

        button.disabled = true;
        label.textContent = 'Шукаю…';
        setStatus('Завантажую дані зі сторінки товару…');

        try {
            const result = await postJson('/api/parse-url', { url });

            if (!result.success) {
                setStatus(result.message || 'Не вдалося прочитати цю сторінку. Заповніть поля вручну.', 'error');
                return;
            }

            const data = result.data;
            fillIfEmpty('wish-title', data.title);
            fillIfEmpty('wish-description', data.description);
            fillIfEmpty('wish-price', data.price);

            if (data.currency) {
                const currency = document.querySelector(`input[name="currency"][value="${data.currency}"]`);
                if (currency) {
                    currency.checked = true;
                }
            }

            if (data.image_url) {
                const image = await postJson('/api/download-image', { image_url: data.image_url }).catch(() => null);

                if (image?.success) {
                    document.getElementById('wish-image-path').value = image.path;
                    showImagePreview(image.url);
                }
            }

            setStatus('Готово. Перевірте поля і збережіть.');
        } catch (error) {
            console.error(error);
            setStatus('Не вдалося прочитати цю сторінку. Заповніть поля вручну.', 'error');
        } finally {
            button.disabled = false;
            label.textContent = 'Заповнити';
        }
    });

    urlInput.addEventListener('input', () => setStatus(defaultHint));
}

// Прев'ю вибраного файлу (фото бажання, аватар)
function initFilePreviews() {
    const wishImage = document.getElementById('wish-image');

    wishImage?.addEventListener('change', () => {
        const file = wishImage.files[0];
        if (file) {
            showImagePreview(URL.createObjectURL(file));
        }
    });

    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files[0];
            const target = document.getElementById(input.dataset.preview);

            if (file && target) {
                target.src = URL.createObjectURL(file);
                target.classList.remove('hidden');
            }
        });
    });
}

// Тости зникають самі; натискання ховає одразу
function initToasts() {
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        const dismiss = () => {
            if (toast.hasAttribute('data-leaving')) {
                return;
            }

            toast.setAttribute('data-leaving', '');
            setTimeout(() => toast.remove(), 250);
        };

        const timer = setTimeout(dismiss, toast.querySelector('.text-danger') ? 6000 : 3500);

        toast.addEventListener('click', () => {
            clearTimeout(timer);
            dismiss();
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initUrlParser();
    initFilePreviews();
    initToasts();
});
