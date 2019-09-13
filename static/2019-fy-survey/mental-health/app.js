var ctx = document.getElementById("mentalHealth").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Yes", "Prefer Not To Say", "No"],
    datasets: [{
      backgroundColor: [
        "#4A6E62", // Dark green
        "#8974A5", // Dark purple
        "#692031", // Dark red
      ],
      data: [30.5, 11.0, 58.6]
    }]
  }
});