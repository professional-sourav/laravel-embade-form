<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="frame-ancestors {{ $parentOrigin ?? '*' }}">
    <style>
        /* CSS Variables that can be overridden via URL parameters */
        :root {
            --form-font-family: {{ $styles['font_family'] ?? 'inherit' }};
            --form-bg-color: transparent;
            --form-text-color: #333;

            --input-padding: 10px;
            --input-border-color: {{ $styles['input_border_color'] ?? '#ddd' }};
            --input-border-radius: {{ $styles['border_radius'] ?? '4px' }};
            --input-bg-color: #fff;
            --input-focus-border: {{ $styles['primary_color'] ?? '#4CAF50' }};

            --label-color: #555;
            --label-font-size: 14px;
            --label-font-weight: 500;

            --button-bg: {{ $styles['button_color'] ?? $styles['primary_color'] ?? '#4CAF50' }};
            --button-color: #fff;
            --button-padding: 12px 24px;
            --button-border-radius: {{ $styles['border_radius'] ?? '4px' }};
            --button-hover-bg: {{ $styles['button_hover_color'] ?? '#45a049' }};

            --error-color: #dc3545;
            --success-color: #28a745;

            --spacing-between-fields: {{ $styles['spacing'] ?? '16px' }};
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--form-font-family);
            background: var(--form-bg-color);
            color: var(--form-text-color);
            padding: 0;
            margin: 0;
        }

        .form-group {
            margin-bottom: var(--spacing-between-fields);
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--label-color);
            font-size: var(--label-font-size);
            font-weight: var(--label-font-weight);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: var(--input-padding);
            border: 1px solid var(--input-border-color);
            border-radius: var(--input-border-radius);
            background-color: var(--input-bg-color);
            color: var(--form-text-color);
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .submit-btn {
            background: var(--button-bg);
            color: var(--button-color);
            padding: var(--button-padding);
            border: none;
            border-radius: var(--button-border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.1s ease;
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--button-hover-bg);
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        .error {
            color: var(--error-color);
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        .success {
            color: var(--success-color);
            padding: 12px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: var(--input-border-radius);
            margin-top: var(--spacing-between-fields);
        }

        /* Loading state */
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<form id="embedForm" data-form-id="{{ $formId }}">
    @csrf
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
        <span class="error" id="error-name"></span>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <span class="error" id="error-email"></span>
    </div>

    <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="4" required></textarea>
        <span class="error" id="error-message"></span>
    </div>

    <button type="submit" class="submit-btn">Submit</button>
    <div id="formMessage"></div>
</form>

<script>
    (function() {
        const form = document.getElementById('embedForm');
        const submitBtn = form.querySelector('.submit-btn');
        const parentOrigin = '{{ $parentOrigin ?? "*" }}';

        // Listen for custom CSS injection from parent
        window.addEventListener('message', function(event) {
            // Security check - only accept from parent origin
            if (parentOrigin !== '*' && event.origin !== parentOrigin) return;

            if (event.data.type === 'injectCSS' && event.data.css) {
                const style = document.createElement('style');
                style.textContent = event.data.css;
                document.head.appendChild(style);

                // Trigger resize after style injection
                setTimeout(updateHeight, 100);
            }
        });

        // Notify parent of height changes
        function updateHeight() {
            const height = document.body.scrollHeight;
            window.parent.postMessage({
                type: 'embedFormResize',
                height: height
            }, parentOrigin);
        }

        // Initial height
        setTimeout(updateHeight, 100);

        // Watch for content changes
        const resizeObserver = new ResizeObserver(updateHeight);
        resizeObserver.observe(document.body);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.getElementById('formMessage').innerHTML = '';

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Add form_id from data attribute
            data.form_id = form.getAttribute('data-form-id');

            try {
                const response = await fetch('{{ route("embed.form.submit") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': data._token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    document.getElementById('formMessage').innerHTML =
                        '<div class="success">' + result.message + '</div>';
                    form.reset();

                    // Notify parent of successful submission
                    window.parent.postMessage({
                        type: 'embedFormSuccess',
                        data: result
                    }, parentOrigin);

                    updateHeight();
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const errorEl = document.getElementById('error-' + field);
                            if (errorEl) {
                                errorEl.textContent = result.errors[field][0];
                            }
                        });
                        updateHeight();
                    } else {
                        document.getElementById('formMessage').innerHTML =
                            '<div class="error">' + (result.message || 'An error occurred.') + '</div>';
                        updateHeight();
                    }
                }
            } catch (error) {
                console.error('Form submission error:', error);
                document.getElementById('formMessage').innerHTML =
                    '<div class="error">An error occurred. Please try again.</div>';
                updateHeight();
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit';
            }
        });
    })();
</script>
</body>
</html>
