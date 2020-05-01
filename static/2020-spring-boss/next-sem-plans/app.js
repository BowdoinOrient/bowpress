var ctx = document.getElementById("next-sem-plans").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Take a semester off", "Take a year off", "Enroll", "Tranfer", "Other"],
    datasets: [{
      backgroundColor: [
        "4A6E62", // Dark Green
        "#692031", //red
        "#8974A5", // Dark purple
        "#CAB1D1",  // Light purple
        "#f1c40f"  //yellow
      ],
      data: [52.3, 5.3, 29.3, 2.3, 10.8]
    }]
  }
});