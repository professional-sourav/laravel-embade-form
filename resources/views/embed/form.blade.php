<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="frame-ancestors {{ $parentOrigin ?? '*' }}">
    <style>
        /* Minimal reset - allow parent site to style */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: inherit;
            background: transparent;
            padding: 0;
            margin: 0;
        }
        /* Only essential structural styles */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            padding: 10px 20px;
            cursor: pointer;
        }
        .error { color: #dc3545; font-size: 14px; margin-top: 5px; }
        .success { color: #28a745; padding: 10px; }
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
        const parentOrigin = '{{ $parentOrigin ?? "*" }}';

        // Notify parent of height changes
        function updateHeight() {
            const height = document.body.scrollHeight;
            window.parent.postMessage({
                type: 'embedFormResize',
                height: height
            }, parentOrigin);
        }

        // Initial height
        updateHeight();

        // Watch for content changes
        const resizeObserver = new ResizeObserver(updateHeight);
        resizeObserver.observe(document.body);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

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
                    }
                }
            } catch (error) {
                document.getElementById('formMessage').innerHTML =
                    '<div class="error">An error occurred. Please try again.</div>';
                updateHeight();
            }
        });
    })();
</script>
</body>
</html>
