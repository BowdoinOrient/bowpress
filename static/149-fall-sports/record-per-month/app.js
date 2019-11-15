var DARK_GREEN = "#4A6E62"
var DARK_RED = "#692031" 
var LIGHT_GREEN ="#A5CFAB" 
var YELLOW = "#f1c40f"
var DARK_PURPLE = "#8974A5"

var WIN = DARK_GREEN
var LOSS = DARK_PURPLE
var TIE = YELLOW

var ctx = document.getElementById("record-per-month").getContext('2d');
var chartData = {
  labels: [
    "Win",
    "Loss",
    "Tie"
  ],
  datasets: [{
    data: [5, 5, 0], //nov
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  }, {
    data: [22, 11, 1], // oct
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  },{
    data: [25, 16, 3], // sept
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