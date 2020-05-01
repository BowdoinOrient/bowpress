var ctx = document.getElementById("productive-at-home").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Productive", "Not productive"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [44.4, 55.6]
    }]
  }
});