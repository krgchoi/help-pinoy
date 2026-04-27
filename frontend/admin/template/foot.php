</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Sidebar Toggle Functionality - FIXED
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById("sidebarToggle");
        const mobileToggle = document.getElementById("mobileToggle");
        const sidebar = document.getElementById("sidebar");

        // Desktop toggle (expand/collapse)
        if (sidebarToggle) {
            sidebarToggle.addEventListener("click", function() {
                sidebar.classList.toggle("expand");
            });
        }

        // Mobile toggle (show/hide)
        if (mobileToggle) {
            mobileToggle.addEventListener("click", function() {
                sidebar.classList.toggle("mobile-open");
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnMobileToggle = mobileToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnMobileToggle && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            }
        });

        // Auto-hide sidebar on mobile when navigating
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-open');
            }
        });
    });

    // Search functionality (if needed)
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
</body>

</html>