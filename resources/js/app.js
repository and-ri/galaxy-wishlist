import './bootstrap';

// URL Parser functionality for wishes
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('wish-url');
    const parseButton = document.getElementById('parse-url-btn');
    const titleInput = document.getElementById('wish-title');
    const descriptionInput = document.getElementById('wish-description');
    const priceInput = document.getElementById('wish-price');
    const currencyInput = document.getElementById('wish-currency');
    const imagePathInput = document.getElementById('wish-image-path');
    const imagePreview = document.getElementById('image-preview');
    const loadingIndicator = document.getElementById('parsing-loading');

    if (parseButton && urlInput) {
        parseButton.addEventListener('click', async function() {
            const url = urlInput.value.trim();
            
            if (!url) {
                alert('Будь ласка, введіть URL');
                return;
            }

            // Show loading
            if (loadingIndicator) {
                loadingIndicator.classList.remove('hidden');
            }
            parseButton.disabled = true;
            parseButton.textContent = 'Завантаження...';

            try {
                // Parse URL metadata
                const response = await fetch('/api/parse-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ url: url })
                });

                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    
                    // Fill form fields
                    if (data.title && titleInput && !titleInput.value) {
                        titleInput.value = data.title;
                    }
                    
                    if (data.description && descriptionInput && !descriptionInput.value) {
                        descriptionInput.value = data.description;
                    }
                    
                    if (data.price && priceInput && !priceInput.value) {
                        priceInput.value = data.price;
                    }
                    
                    if (data.currency && currencyInput) {
                        currencyInput.value = data.currency;
                    }
                    
                    // Download and save image
                    if (data.image_url) {
                        await downloadImage(data.image_url);
                    }
                } else {
                    alert('Помилка парсингу URL: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Помилка при обробці URL: ' + error.message);
            } finally {
                if (loadingIndicator) {
                    loadingIndicator.classList.add('hidden');
                }
                parseButton.disabled = false;
                parseButton.textContent = '🔍 Автозаповнення';
            }
        });
    }

    async function downloadImage(imageUrl) {
        try {
            const response = await fetch('/api/download-image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ image_url: imageUrl })
            });

            const result = await response.json();

            if (result.success) {
                if (imagePathInput) {
                    imagePathInput.value = result.path;
                }
                
                if (imagePreview) {
                    imagePreview.src = result.url;
                    imagePreview.classList.remove('hidden');
                    const previewContainer = document.getElementById('image-preview-container');
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                    }
                }
            }
        } catch (error) {
            console.error('Error downloading image:', error);
        }
    }

    // Image upload preview
    const imageInput = document.getElementById('wish-image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        const previewContainer = document.getElementById('image-preview-container');
                        if (previewContainer) {
                            previewContainer.classList.remove('hidden');
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
