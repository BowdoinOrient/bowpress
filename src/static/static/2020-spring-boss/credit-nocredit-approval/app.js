var ctx = document.getElementById("credit-nocredit-approval").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Approve", "Disapprove"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [86.1, 13.9]
    }]
  }
});