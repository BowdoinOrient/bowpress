var LIGHT_GREEN ="#A5CFAB";
var DARK_GREEN = "#4A6E62";
var YELLOW = "#f1c40f";
var DARK_RED = "#692031";
var DARK_PURPLE = "#8974A5";
var LIGHT_BLUE = "#48639C";
var DARK_BLUE = "#1D3461";

var WIN = DARK_GREEN;
var LOSS = DARK_PURPLE;
var TIE = LIGHT_BLUE;

let OVERALL = [70, 62, 4]
let M_TEAMS = [30, 35, 1]
let W_TEAMS = [40, 27, 3]

var chartData = {
  labels: [
    "Win",
    "Loss",
    "Tie"
  ],
  datasets: [{
    data: OVERALL,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  }, {
    data: M_TEAMS,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  },{
    data: W_TEAMS,
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

var ctx = document.getElementById("record-overall").getContext('2d');
var pieChart = new Chart(ctx, {
  type: 'pie',
  data: chartData
})