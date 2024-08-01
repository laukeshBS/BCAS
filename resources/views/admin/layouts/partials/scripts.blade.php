<!-- jquery latest version -->
<script src="{{ asset('public/admin/assets/js/vendor/jquery-2.2.4.min.js') }}"></script>
<!-- bootstrap 4 js -->
<script src="{{ asset('public/admin/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/jquery.slicknav.min.js') }}"></script>

<!-- start chart js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<!-- start highcharts js -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<!-- start zingchart js -->
<script src="https://cdn.zingchart.com/zingchart.min.js"></script>
<script>
zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
</script>
<!-- all line chart activation -->
<script src="{{ asset('public/admin/assets/js/line-chart.js') }}"></script>
<!-- all pie chart -->
<script src="{{ asset('public/admin/assets/js/pie-chart.js') }}"></script>
<!-- others plugins -->
<script src="{{ asset('public/admin/assets/js/plugins.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/scripts.js') }}"></script>
<!-- validations -->
<script src="{{ asset('public/admin/assets/js/custom.js') }}"></script>
<script src="{{ asset('public/admin/assets/js/validate.js') }}"></script>
<script src="{{ URL::asset('/public/admin/assets/summernote/summernote-bs4.js')}}"></script>
<script>
        $(document).ready(function() {
           // alert("ok");
            $('.summernote-simple').summernote({
                placeholder: 'Enter text here...',
                tabsize: 2,
                height: 200
            });
        });
    </script>