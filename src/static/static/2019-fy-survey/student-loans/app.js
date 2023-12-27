var ctx = document.getElementById("studentLoans").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Yes", "No, but we plan to later", "No, and we don't plan to"],
    datasets: [{
      backgroundColor: [
        "#4A6E62", // Dark green
        "#692031", // Dark red
        "#8974A5", // Dark purple
      ],
      data: [15.7, 14.8, 69.5]
    }]
  }
});