{strip}
    </div>
    </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.bd').on('mouseover', function() {
                var src =  $(this).find('img')[0].src;
                if(src && src.length > 0 && src.indexOf('hover') < 0) {
                    $(this).find('img')[0].src = src.split('.png')[0] + '-hover.png';
                }
            });
            $('.bd').on('mouseout', function() {
                var src =  $(this).find('img')[0].src;
                if(src && src.length > 0 && src.indexOf('hover') > -1) {
                    $(this).find('img')[0].src = src.split('-hover')[0] + '.png';
                }
            });
            $('body').on('click', '.group-inner .fa-chevron-down' ,function() {
            var left = $('.group-inner').offset().left;
            $('.menu-nav').css('left', left);
            $('.menu-nav').show();
            $(this).removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }).on('click','.group-inner .fa-chevron-up' ,function() {
                $('.menu-nav').hide();
                $(this).removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

            $('.nav-con p').on('mouseover',function() {
                $(this).css('color', 'rgba(255, 255,255,1)');
            });
            $('.nav-con p').on('mouseout',function() {
                $(this).css('color', 'rgba(255, 255,255,0.76)');
            });
            $('.username').on('mouseover', function() {
                $('.userinfo-con').show();
            });
            $('.userinfo p').on('mouseover', function() {
                $(this).css('color', 'rgba(255,255,255,0.76)');
            });
            $('.userinfo p').on('mouseout', function() {
                $(this).css('color', 'rgba(255, 255,255, 1)');
            });
            $('.userinfo-con').on('mouseleave', function() {
                $(this).hide();
            });
        })
    </script>
    </body>
    </html>
{/strip}
