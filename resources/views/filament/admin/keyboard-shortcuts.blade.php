<script>
    document.addEventListener('keydown', (e) => {
        const isSearchShortcut = (e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K');

        if (isSearchShortcut) {
            e.preventDefault();
            const input = document.querySelector('.fi-global-search-input input[type="search"]');
            if (input) {
                input.focus();
                input.select();
            }
        }
    });
</script>
