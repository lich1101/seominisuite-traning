(function ($) {
    function lineChart(){
        let maxNumber = 100;
        let xValues = jQuery("#ValueX").val();
        let lineDataSet = jQuery("#lineDataSet").val();
        console.log("$lineDataSet",lineDataSet);
        new Chart("lineChart", {
            type: "line",
            data: {
                labels: JSON.parse(xValues),
                datasets: [
                    {
                        label:"Thứ hạng trung bình",
                        data: JSON.parse(lineDataSet),
                        borderColor: "#0084ff",
                        backgroundColor: "#0084ff",
                        fill: false,
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        // display:false,
                        position:"bottom",
                        align:"center"
                    }
                },
                scales: {
                    y: {
                        reverse: true,
                        beginAtZero:false,
                        min: 1, max:parseInt(maxNumber)
                    },
                }
            }
        });
    }
    function barChart(){
        let maxNumber = $("#maxNumber").val();
        var ctx = document.getElementById("barChart");
        let barChartDataSet1 = $("#barChartDataSet1").val();
        console.log("barChartDataSet1",JSON.parse(barChartDataSet1));
        console.log("barChartDataSet1",barChartDataSet1.length);
        let barChartDataSet2 = $("#barChartDataSet2").val();
        let labels = [];
        let dataSet1_colors = [];
        let dataSet2_colors = [];
        let dataSet1Label = $("#barChartDataSet1").attr("data-label");
        let dataSet2Label = $("#barChartDataSet2").attr("data-label");
        let display = $("#barChartDataSet1").attr("display");
        for(let i=1;i<=10;i++){
            let key = "Top "+i;
            labels.push(key);
            dataSet1_colors.push('#1f9f4c');
            dataSet2_colors.push('#f8a03b');
        }
        let dataSets = [];
        dataSets.push({
            label: dataSet1Label,
            data: JSON.parse(barChartDataSet1),
            backgroundColor: dataSet1_colors,
            borderColor: dataSet1_colors,
            borderWidth: 1
        });
        if(dataSet2Label.length>0){
            dataSets.push( {
                label: dataSet2Label,
                data: JSON.parse(barChartDataSet2),
                backgroundColor: dataSet2_colors,
                borderColor: dataSet2_colors,
                borderWidth: 1
            });
        }
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets:dataSets
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        display:display,
                        position: 'bottom',
                        align:'center',

                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max:parseInt(maxNumber),
                    }
                }
            }
        });
    }
    $("document").ready(function (e) {
        barChart();
        lineChart();

    });
})(jQuery);