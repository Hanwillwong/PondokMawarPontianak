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
  
  <!-- PWA  -->
<meta name="theme-color" content="#6777ef"/>
<link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
<link rel="manifest" href="{{ asset('/manifest.json') }}">

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




<script src="{{ secure_asset('js/jquery.min.js') }}" defer></script>
<script src="{{ secure_asset('js/bootstrap.min.js') }}" defer></script>
<script src="{{ secure_asset('js/bootstrap-select.min.js') }}" defer></script>   
<script src="{{ secure_asset('js/sweetalert.min.js') }}" defer></script>    
<script src="{{ secure_asset('js/apexcharts/apexcharts.js') }}" defer></script>
<script src="{{ secure_asset('js/main.js') }}" defer></script>
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
<script>
    // Register service worker
    navigator.serviceWorker.register('/service-worker.js').then(registration => {
        console.log('Service Worker registered:', registration);

        // Minta izin
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                // Lanjut ke subscription
                registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array("{{ env('VAPID_PUBLIC_KEY') }}")
                }).then(sub => {
                    console.log("Subscription berhasil:", sub); // <--- LETAKKAN DI SINI

                    // Kirim ke server
                    fetch("{{ url('/admin/save-subscription') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ sub: JSON.stringify(sub) })
                    }).then(res => res.json())
                      .then(data => {
                          console.log("Subscription saved:", data);
                      });
                }).catch(err => {
                    console.error("Subscription gagal:", err);
                });
            }
        });
    });

    // Konversi VAPID public key
    function urlBase64ToUint8Array(base64String) {
        const padding = "=".repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, "+").replace(/_/g, "/");
        const rawData = atob(base64);
        return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
    }
</script>

<script src="{{ secure_asset('/sw.js') }}" defer></script>
<script>
if ("serviceWorker" in navigator) {
    // Register a service worker hosted at the root of the
    // site using the default scope.
    navigator.serviceWorker.register("/sw.js").then(
    (registration) => {
        console.log("Service worker registration succeeded:", registration);
    },
    (error) => {
        console.error(`Service worker registration failed: ${error}`);
    },
    );
} else {
    console.error("Service workers are not supported.");
}
</script>


</body>
<!-- [Body] end -->

</html>