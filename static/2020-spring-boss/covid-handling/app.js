var ctx = document.getElementById("covid-handling").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Well or very well", "Poor or very poorly"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [85, 15]
    }]
  }
});