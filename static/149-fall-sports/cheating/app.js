var DARK_GREEN = "#4A6E62"
var DARK_RED = "#692031" 
var LIGHT_GREEN ="#A5CFAB" 
var YELLOW = "#f1c40f"
var DARK_PURPLE = "#8974A5"
var LIGHT_PURPLE = "#CAB1D1"
var HOMEPAGE_GREEN = "#466E49"
var RED = "#ED1D25"

var ctx = document.getElementById("cheating").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Yes", "No"],
    datasets: [{
      backgroundColor: [
        "#CAB1D1",  // Light purple
        "#8974A5", // Dark purple
      ],
      data: [24.8, 75.2]
    }]
  }
});

