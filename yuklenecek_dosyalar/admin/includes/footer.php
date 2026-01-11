</div>
</main>
</div>

<script>
    function toggleMobileMenu() {
        document.body.classList.toggle('sidebar-open');
        const icon = document.getElementById('menuIcon');
        icon.textContent = document.body.classList.contains('sidebar-open') ? 'close' : 'menu';
    }
</script>
</body>

</html>