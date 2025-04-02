        </div><!-- End main-section -->
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Simple Stock Market. All rights reserved.</p>
        </footer>
    </div><!-- End container -->
    
    <script>
        $(document).ready(function() {
            // Toggle mobile navigation
            $('#menu-toggle').on('click', function() {
                $('#menu-items').toggleClass('show');
                let expanded = $(this).attr('aria-expanded') === 'true';
                $(this).attr('aria-expanded', !expanded);
            });
            
            // Keyboard accessibility for menu
            $('#menu-items a').on('keydown', function(e) {
                const items = $('#menu-items a');
                const index = items.index(this);
                const key = e.which;
                
                // Left arrow
                if (key === 37 && index > 0) {
                    e.preventDefault();
                    items[index - 1].focus();
                }
                // Right arrow
                else if (key === 39 && index < items.length - 1) {
                    e.preventDefault();
                    items[index + 1].focus();
                }
                // Escape key
                else if (key === 27) {
                    $('#menu-items').removeClass('show');
                    $('#menu-toggle').attr('aria-expanded', 'false').focus();
                }
            });
            
            // Stock chart tabs
            $('.tab-buttons button').on('click', function() {
                const tabId = $(this).attr('data-tab');
                
                // Update active tab button
                $('.tab-buttons button').removeClass('active');
                $(this).addClass('active');
                
                // Show selected tab content
                $('.tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
            });
            
            // Collapsible stock descriptions
            $('.collapsible-header').on('click', function() {
                $(this).toggleClass('active');
                const content = $(this).next('.collapsible-content');
                
                if (content.css('max-height') !== '0px') {
                    content.css('max-height', '0px');
                    $(this).attr('aria-expanded', 'false');
                } else {
                    content.css('max-height', content.prop('scrollHeight') + 'px');
                    $(this).attr('aria-expanded', 'true');
                }
            });
            
            // Initialize first tab as active
            if ($('.tab-buttons button').length) {
                $('.tab-buttons button:first').addClass('active');
                $('.tab-content:first').addClass('active');
            }
        });
    </script>
</body>
</html> 