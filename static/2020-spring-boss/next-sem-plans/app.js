var ctx = document.getElementById("next-sem-plans").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Take a semester off", "Enroll", "Other"],
    datasets: [{
      backgroundColor: [
        "4A6E62", // Dark Green
        "#8974A5", // Dark purple
        "#CAB1D1",  // Light purple
      ],
      data: [52.3, 29.3, 18.4]
    }]
  }
});