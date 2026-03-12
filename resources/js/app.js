import './bootstrap';
import Alpine from 'alpinejs';

Alpine.data('signedUrlModal', (signedUrlRoute) => ({
    filePath: '',
    expiry: 10,
    url: '',
    copied: false,
    error: '',
    loading: false,
    openModal(path) {
        this.filePath = path;
        this.$refs.modal.showModal();
    },
    reset() {
        this.filePath = '';
        this.expiry = 10;
        this.url = '';
        this.copied = false;
        this.error = '';
        this.loading = false;
    },
    async generate() {
        this.loading = true;
        this.error = '';

        try {
            const response = await fetch(signedUrlRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ path: this.filePath, expiry: this.expiry }),
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.message || 'Failed to generate signed URL.');
            }

            const data = await response.json();
            this.url = data.url;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
    async copy() {
        await navigator.clipboard.writeText(this.url);
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
    }
}));

window.Alpine = Alpine;
Alpine.start();
