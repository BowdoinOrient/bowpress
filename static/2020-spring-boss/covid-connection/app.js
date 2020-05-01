var ctx = document.getElementById("covid-connection").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["No connection", "Have a connection"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [57.2, 42.8]
    }]
  }
});