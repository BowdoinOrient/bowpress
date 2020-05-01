var ctx = document.getElementById("wifi-connection").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Very good or good", "Ok", "Bad or very bad"],
    datasets: [{
      backgroundColor: [
        "4A6E62", // Dark Green
        "#8974A5", // Dark purple
        "#CAB1D1",  // Light purple
      ],
      data: [61, 27.2, 11.8]
    }]
  }
});