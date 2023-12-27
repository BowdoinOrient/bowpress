// let LIGHT_GREEN ="#A5CFAB" 
// let DARK_GREEN = "#4A6E62"
// let YELLOW = "#f1c40f"
// let DARK_RED = "#692031" 
// let DARK_PURPLE = "#8974A5"
// let LIGHT_BLUE = "#48639C"
// let DARK_BLUE = "#1D3461"

// let WIN = DARK_GREEN
// let LOSS = DARK_PURPLE
// let TIE = LIGHT_BLUE

let ALL_GAMES = [41, 47, 2];
let M_GAMES = [21, 24, 1];
let W_GAMES = [20, 23, 1];

var chartData = {
  labels: [
    "Win",
    "Loss",
    "Tie"
  ],
  datasets: [{
    data: ALL_GAMES,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  }, {
    data: M_GAMES,
    backgroundColor: [
      WIN,
      LOSS,
      TIE
    ],
  },{
    data: W_GAMES,
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

var ctx = document.getElementById("record-overall-conference").getContext('2d');
var pieChart = new Chart(ctx, {
  type: 'pie',
  data: chartData
})