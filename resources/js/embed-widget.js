// embed-widget.js - Provide this to clients
(function() {
    class EmbedFormWidget {
        constructor(options) {
            this.container = options.container;
            this.formId = options.formId;
            this.apiUrl = options.apiUrl || 'https://testherd.test';
            this.onSuccess = options.onSuccess || function() {};
            this.init();

            console.log("API URL", this.apiUrl)
        }

        init() {
            const iframe = document.createElement('iframe');
            iframe.src = `${this.apiUrl}/embed/form?form_id=${this.formId}`;
            iframe.style.width = '100%';
            iframe.style.border = 'none';
            iframe.style.overflow = 'hidden';
            iframe.setAttribute('scrolling', 'no');
            iframe.setAttribute('allow', 'same-origin');

            this.iframe = iframe;

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
