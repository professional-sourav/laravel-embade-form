// embed-widget.js - Enhanced version
(function() {
    class EmbedFormWidget {
        constructor(options) {
            this.container = options.container;
            this.formId = options.formId;
            this.apiUrl = options.apiUrl || 'https://testherd.test';
            this.styles = options.styles || {};
            this.customCSS = options.customCSS || '';
            this.onSuccess = options.onSuccess || function() {};
            this.init();
        }

        init() {
            const iframe = document.createElement('iframe');

            console.log("Iframe style", this.styles)

            // Build URL with style parameters
            const params = new URLSearchParams({
                form_id: this.formId,
                // Pass style parameters via URL
                primary_color: this.styles.primaryColor || '',
                button_color: this.styles.buttonColor || '',
                font_family: this.styles.fontFamily || '',
                border_radius: this.styles.borderRadius || '',
                input_border_color: this.styles.inputBorderColor || '',
                spacing: this.styles.spacing || ''
            });

            // Remove empty params
            for (let [key, value] of params.entries()) {
                if (!value) params.delete(key);
            }

            iframe.src = `${this.apiUrl}/embed/form?${params.toString()}`;
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.border = 'none';
            iframe.style.overflow = 'hidden';
            iframe.setAttribute('scrolling', 'no');
            iframe.setAttribute('allow', 'same-origin');

            this.iframe = iframe;

            // Wait for iframe to load before injecting CSS
            iframe.addEventListener('load', () => {
                if (this.customCSS) {
                    iframe.contentWindow.postMessage({
                        type: 'injectCSS',
                        css: this.customCSS
                    }, this.apiUrl);
                }
            });

            // Listen for messages from iframe
            window.addEventListener('message', (event) => {
                if (event.origin !== this.apiUrl) return;

                switch(event.data.type) {
                    case 'embedFormResize':
                        iframe.style.height = event.data.height + 'px';
                        break;
                    case 'embedFormSuccess':
                        this.onSuccess(event.data.data);
                        break;
                }
            });

            this.container.appendChild(iframe);
        }
    }

    window.EmbedFormWidget = EmbedFormWidget;
})();
