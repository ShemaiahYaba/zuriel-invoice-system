</div> <!-- End container -->
    
    <!-- Footer -->
    <footer class="mt-5 py-3 bg-light text-center">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo Config::get('COMPANY_NAME'); ?>. All rights reserved.</p>
        <small><?php echo Config::get('COMPANY_TAGLINE'); ?></small>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>