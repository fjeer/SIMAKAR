@push('scripts')
    <script>
        (() => {
            const openDialog = (id) => document.getElementById(id)?.showModal();

            document.querySelectorAll('[data-dialog-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => openDialog(trigger.dataset.dialogOpen));
            });

            document.querySelectorAll('[data-dialog-close]').forEach((trigger) => {
                trigger.addEventListener('click', () => trigger.closest('dialog')?.close());
            });

            document.querySelectorAll('dialog[data-dismissible]').forEach((dialog) => {
                dialog.addEventListener('click', (event) => {
                    if (event.target === dialog) {
                        dialog.close();
                    }
                });
            });
        })();
    </script>
@endpush
