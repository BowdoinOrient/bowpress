var DARK_GREEN = "#4A6E62"
var DARK_RED = "#692031" 
var LIGHT_GREEN ="#A5CFAB" 
var YELLOW = "#f1c40f"
var DARK_PURPLE = "#8974A5"

var WIN = DARK_GREEN
var LOSS = DARK_PURPLE
var TIE = YELLOW

var ctx = document.getElementById("record-home-away").getContext('2d');
var chartData = {
  labels: [
    "Win",
    "Loss",
    "Tie"
  ],
  datasets: [{
    data: [25, 22, 3],
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  }, {
    data: [20, 13, 4], // Inside --> Home
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