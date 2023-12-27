var ctx = document.getElementById("homePopulationDensity").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Urban", "Suburban", "Rural"],
    datasets: [{
      backgroundColor: [
        "#4A6E62", // Dark green
        "#8974A5", // Dark purple
        "#692031", // Dark red
      ],
      data: [28.6, 56.7, 14.8]
    }]
  }
});