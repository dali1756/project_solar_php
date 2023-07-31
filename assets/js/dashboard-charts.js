var trafficchart = document.getElementById("trafficflow");
var saleschart = document.getElementById("sales");

// new
var myChart1 = new Chart(trafficchart, {
type: 'line',
data: {
    labels: ['00:00','01:00', '02:00', '03:00','04:00', '05:00','06:00','07:00', '08:00','09:00', '10:00','11:00','12:00','13:00','14:00','15:00','16:00', '17:00','18:00', '19:00','20:00','21:00','22:00', '23:00','24:00'],
    datasets: [{
        label: '輸入電量',
        data: ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.01', '0.01', '0.05','0.05','0.05', '0.06', '0.05', '0.01', '0.00','0.00', '0.00', '0.00','0.00', '0.00', '0.00','0.00','0.00'],
        backgroundColor: "rgba(48, 164, 255, 0.2)",
        borderColor: "rgba(48, 164, 255, 0.8)",
        fill: true,
        borderWidth: 2
    }]
},
options: {
    animation: {
        duration: 2000,
        easing: 'easeOutQuart',
    },
    plugins: {
        
        legend: {
            display: true,
            position: 'top',
        },
        title: {
            display: true,
            text: 'Kw',
            position: 'left',
        },
    },
}
});

// new
var myChart2 = new Chart(saleschart, {
type: 'bar',
data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
            label: '收益',
            data: ["280", "300", "400", "600", "450", "400", "500", "550", "450", "650", "950", "1000"],
            backgroundColor: "rgba(76, 175, 80, 0.5)",
            borderColor: "#6da252",
            borderWidth: 2,
    }]
},
options: {
    animation: {
        duration: 2000,
        easing: 'easeOutQuart',
    },
    plugins: {
        legend: {
            display: true,
            position: 'top',
        },
        title: {
            display: true,
            text: 'Thousand',
            position: 'left',
        },
    },
}
});
