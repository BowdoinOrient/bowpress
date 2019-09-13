var ctx = document.getElementById("myPieChart").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["4A6E62", "8876A0", "692031", "A5CFAB", "f1c40f", "8974A5", "CAB1D1"],
    datasets: [{
      backgroundColor: [
        "#4A6E62", // Dark green
        "#8876A0", // Medium purple ( Don't use)
        "#692031", // Dark red
        "#A5CFAB", // Light green
        "#f1c40f", // Yellow
        "#8974A5", // Dark purple
        "#CAB1D1"  // Light purple 
      ],
      data: [12, 19, 3, 17, 28, 19, 7]
    }]
  }
});