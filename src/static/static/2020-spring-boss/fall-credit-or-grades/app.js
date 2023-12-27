var ctx = document.getElementById("fall-credit-or-grades").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Universal credit/no credit", "Letter grades"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [66.6, 33.4]
    }]
  }
});