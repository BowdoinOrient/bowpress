var ctx = document.getElementById("sync-async-pref").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Synchronous", "Asynchronous"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [57.8, 42.2]
    }]
  }
});