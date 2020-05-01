// Actually not displaced, using Gwen's graphic instead

var ctx = document.getElementById("online-or-jan-start").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Online fall", "No fall/Jan. start"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [23.9, 76.1]
    }]
  }
});