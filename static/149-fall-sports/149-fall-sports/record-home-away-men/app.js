var DARK_GREEN = "#4A6E62"
var DARK_RED = "#692031" 
var LIGHT_GREEN ="#A5CFAB" 
var YELLOW = "#f1c40f"
var DARK_PURPLE = "#8974A5"

var WIN = DARK_GREEN
var LOSS = DARK_PURPLE
var TIE = YELLOW

var ctx = document.getElementById("record-home-away-men");
var chartData = {
  labels: [
    "Win",
    "Loss",
    "Tie"
  ],
  datasets: [{
    data: [5, 8, 3],
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  }, {
    data: [1, 6, 3], // Home 
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  },
   {
    data: [0]
  }]
};
var pieChart = new Chart(ctx, {
  type: 'pie',
  data: chartData
})