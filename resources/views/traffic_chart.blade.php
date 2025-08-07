<!DOCTYPE html>
<html>
<head>
    <title>Traffic Interface Mikrotik</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2 style="text-align:center">Live Interface Traffic (ether1–ether10)</h2>
    <div id="container" style="width: 95%; height: 600px; margin: auto;"></div>

    <script>
        const interfaces = ['ether1','ether2','ether3','ether4','ether5','ether6','ether7','ether8','ether9','ether10'];
        const seriesRx = interfaces.map(iface => ({
            name: iface + ' RX',
            data: []
        }));
        const seriesTx = interfaces.map(iface => ({
            name: iface + ' TX',
            data: []
        }));

        const chart = Highcharts.chart('container', {
            chart: {
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
                events: {
                    load: fetchData
                }
            },
            title: { text: 'Live Traffic per Interface (bps)' },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: { text: 'Bits per Second (bps)' },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                shared: true,
                valueSuffix: ' bps'
            },
            legend: { enabled: true },
            series: [...seriesRx, ...seriesTx]
        });

        function fetchData() {
            setInterval(function () {
                fetch('{{ route('api.traffic.all') }}')
                    .then(res => res.json())
                    .then(data => {
                        const x = (new Date()).getTime();
                        interfaces.forEach((iface, i) => {
                            const rx = data[iface]?.rx || 0;
                            const tx = data[iface]?.tx || 0;

                            // Push RX data
                            if (chart.series[i]) {
                                chart.series[i].addPoint([x, rx], false, chart.series[i].data.length > 20);
                            }

                            // Push TX data
                            if (chart.series[i + interfaces.length]) {
                                chart.series[i + interfaces.length].addPoint([x, tx], false, chart.series[i + interfaces.length].data.length > 20);
                            }
                        });
                        chart.redraw();
                    });
            }, 2000);
        }
    </script>
</body>
</html>
