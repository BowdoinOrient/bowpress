// var DARK_GREEN = "#4A6E62"
// var DARK_RED = "#692031" 
// var LIGHT_GREEN ="#A5CFAB" 
// var YELLOW = "#f1c40f"
// var DARK_PURPLE = "#8974A5"

// var WIN = DARK_GREEN
// var LOSS = DARK_PURPLE
// var TIE = YELLOW

let NOVEMBER_RECORD = [13, 7, 1];
let DECEMBER_RECORD = [10, 4];
let JANUARY_RECORD = [18, 16, 3];
let FEBRUARY_RECORD = [10, 15];

var ctx = document.getElementById("record-per-month").getContext('2d');
var chartData = {
  labels: [
    "Win",
    "Loss",
    "Tie"
  ],
  datasets: [{
    data: FEBRUARY_RECORD,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  }, {
    data: JANUARY_RECORD,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  },{
    data: DECEMBER_RECORD,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  },{
    data: NOVEMBER_RECORD,
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