<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Pondok Mawar</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="user-logged-in" content="{{ auth()->check() ? 'true' : 'false' }}">
  <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/animate.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/animation.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/bootstrap.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ secure_asset('font/fonts.css') }}">
  <link rel="stylesheet" href="{{ secure_asset('icon/style.css') }}">
  <link rel="shortcut icon" href="{{ secure_asset('images/favicon.ico') }}">
  <link rel="apple-touch-icon-precomposed" href="{{ secure_asset('images/favicon.ico') }}">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/sweetalert.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/custom.css') }}">
  
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body class="body">
<div id="wrapper">
  <div id="page">
    <div class="layout-wrap">
      @include('dashboard.sidebar')
      <div class="section-content-right">
        @include('dashboard.navbar')

        <!-- Main Content -->
        <div class="main-content">
            @yield('container')
            @include('dashboard.footer')
        </div>
      </div>
    </div>
  </div>
</div>




<script src="{{ secure_asset('js/jquery.min.js') }}"></script>
<script src="{{ secure_asset('js/bootstrap.min.js') }}"></script>
<script src="{{ secure_asset('js/bootstrap-select.min.js') }}"></script>   
<script src="{{ secure_asset('js/sweetalert.min.js') }}"></script>    
<script src="{{ secure_asset('js/apexcharts/apexcharts.js') }}"></script>
<script src="{{ secure_asset('js/main.js') }}"></script>
<script>
    (function ($) {

        var tfLineChart = (function () {

            var chartBar = function () {

                var options = {
                    series: [{
                        name: 'Total',
                        data: [0.00, 0.00, 0.00, 0.00, 0.00, 273.22, 208.12, 0.00, 0.00, 0.00, 0.00, 0.00]
                    }, {
                        name: 'Pending',
                        data: [0.00, 0.00, 0.00, 0.00, 0.00, 273.22, 208.12, 0.00, 0.00, 0.00, 0.00, 0.00]
                    },
                    {
                        name: 'Delivered',
                        data: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00]
                    }, {
                        name: 'Canceled',
                        data: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00]
                    }],
                    chart: {
                        type: 'bar',
                        height: 325,
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '10px',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false,
                    },
                    colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
                    stroke: {
                        show: false,
                    },
                    xaxis: {
                        labels: {
                            style: {
                                colors: '#212529',
                            },
                        },
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return "$ " + val + ""
                            }
                        }
                    }
                };

                chart = new ApexCharts(
                    document.querySelector("#line-chart-8"),
                    options
                );
                if ($("#line-chart-8").length > 0) {
                    chart.render();
                }
            };

            /* Function ============ */
            return {
                init: function () { },

                load: function () {
                    chartBar();
                },
                resize: function () { },
            };
        })();

        jQuery(document).ready(function () { });

        jQuery(window).on("load", function () {
            tfLineChart.load();
        });

        jQuery(window).on("resize", function () { });
    })(jQuery);
</script>
@stack('scripts')

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
  if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js').then(function(registration) {
          askPermission().then(() => {
              subscribeUser(registration);
          });
      });
  }

  function askPermission() {
      return Notification.requestPermission();
  }

  function subscribeUser(registration) {
      registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(document.querySelector('meta[name="vapid-public-key"]').content)
      }).then(function(subscription) {
          axios.post('/save-subscription', subscription);
      });
  }

  function urlBase64ToUint8Array(base64String) {
      const padding = '='.repeat((4 - base64String.length % 4) % 4);
      const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
      const rawData = atob(base64);
      return new Uint8Array([...rawData].map(char => char.charCodeAt(0)));
  }
  </script>
</body>
<!-- [Body] end -->

</html>