
<!-- footer area start-->
<footer>
    <div class="footer-area">
        <p>© Copyright 2024. All right reserved. Template by <a href="#">CSC</a>.</p>
    </div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<script>
    // autologout.js

        $(document).ready(function () {
            const timeout = 150000000;  
            var idleTimer = null;
            $('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
                clearTimeout(idleTimer);

                idleTimer = setTimeout(function () {
                    document.getElementById('admin-logout-form').submit();
                }, timeout);
            });
            $("body").trigger("mousemove");
        });
        // $.sessionTimeout({
        //     keepAliveUrl: 'keep-alive.html',
        //     logoutUrl: 'login.html',
        //     redirUrl: 'locked.html',
        //     warnAfter: 3000,
        //     redirAfter: 10000,
        //     countdownMessage: 'Redirecting in {timer} seconds.'
        // });

    //     function checkSession() {
    //         $.post('{{ route('session.ajax.check') }}', { '_token' : '{!! csrf_token() !!}' }, function(data) {
    //             //alert('if ok');
    //             //alert('else heres'+data);
    //                 if (data == 'loggedOut') {
    //                     // User was logged out. Redirect to login page
    //                 // alert('if'+data);
    //                     document.location.href = '{!! route('login') !!}';
    //                 }
    //                 else if (data != '') {
    //                     // User will be logged out soon. 
    //                     // TODO display proper modal, instead of console.log()
    //                     alert('else'+data);
    //                     console.log(data);
    //                 }
    //         });
    //     }

    // setInterval(checkSession, 5000);
</script>

<!-- footer area end-->