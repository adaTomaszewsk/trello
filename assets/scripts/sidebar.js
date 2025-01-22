window.addEventListener('load', function () {
    const sidebarItems = document.querySelectorAll('.sidebar-item');

    sidebarItems.forEach(item => {
        if (item.href === window.location.href) {
            item.classList.add('active');
        }
    });
});