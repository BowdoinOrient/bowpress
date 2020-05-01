var ctx = document.getElementById("safe-living").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Yes", "No"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [96.9, 3.1]
    }]
  }
});