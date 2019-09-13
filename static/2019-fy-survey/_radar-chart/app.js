var ctx = document.getElementById("myRadarChart");
var myChart = new Chart(ctx, {
  type: 'radar',
  data: {
    labels: ["M", "T", "W", "T", "F", "S", "S"],
    datasets: [{
      label: 'apples',
      backgroundColor: "rgba(153,255,51,0.4)",
      borderColor: "rgba(153,255,51,1)",
      data: [12, 19, 3, 17, 28, 24, 7]
    }, {
      label: 'oranges',
      backgroundColor: "rgba(255,153,0,0.4)",
      borderColor: "rgba(255,153,0,1)",
      data: [30, 29, 5, 5, 20, 3, 10]
    }]
  }
});