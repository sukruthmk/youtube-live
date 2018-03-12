$( document ).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var searchButton = $("#search-button");
    searchButton.on("click", function() {
        var searchInput = $("#search-user").val();
        var liveStreamId = $("#live-stream-id").val();
        $.ajax({
             type: 'POST',
             url: 'live-search',
             data: { name: searchInput, id: liveStreamId},
             success: function(result){
                 var html = "";
                 for(var key in result) {
                     var item = result[key];
                     var line = "<tr>" + "<td>" + item["usr_name"] + "</td><td>" + item["msg"] + "</td></tr>";
                     html = html + line;
                 }
                 console.log(html);
                $(".serach-table .search-body").html(html);
             }
        });
    });

    $("#post").on("click", function() {
        var msg = $("#post-msg").val();
        var liveStreamId = $("#live-stream-id").val();
        $.ajax({
             type: 'POST',
             url: 'live-post',
             data: { msg: msg, id: liveStreamId},
             success: function(result){
                 var html = $(".message-container .message-div").html();
                 html = '<div class="row message-div">'+html+"</div>";
                 $(".message-container").append(html);
                 $(".message-container .message-div").last().find('.message').text(msg);
             }
        });
    });

    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    Highcharts.chart('highcharts-container', {
        chart: {
            type: 'spline',
            animation: Highcharts.svg, // don't animate in old IE
            marginRight: 10,
            events: {
                load: function () {

                    // set up the updating of the chart each second
                    var series = this.series[0];
                    setInterval(function () {
                        var x = (new Date()).getTime(), // current time
                            y = Math.random();
                        series.addPoint([x, y], true, true);
                    }, 1000);
                }
            }
        },
        title: {
            text: 'Haype Chart'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150
        },
        yAxis: {
            title: {
                text: 'Value'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    //Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                    Highcharts.numberFormat(this.y, 2);
            }
        },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        series: [{
            name: 'Chats Per Second',
            data: (function () {
                // generate an array of random data
                var data = [],
                    time = (new Date()).getTime(),
                    i;

                for (i = -19; i <= 0; i += 1) {
                    data.push({
                        x: time + i * 1000,
                        y: Math.random()
                    });
                }
                return data;
            }())
        }]
    });
});
