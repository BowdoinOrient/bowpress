let LIGHT_GREEN ="#A5CFAB"
let DARK_GREEN = "#4A6E62"
let YELLOW = "#f1c40f"
let DARK_RED = "#692031" 
let DARK_PURPLE = "#8974A5"
let LIGHT_BLUE = "#48639C"
let DARK_BLUE = "#1D3461"

let W_BBALL = [null, null, null, null, null, 1, 1, 1, 2, 2, 2, 2];
let M_BBALL = [null, null, null, null, null, 4, 9, 9, 10, 8, 9, 9];
let W_HOCKEY = [1, 3, 3, 3, 3, 5, 6, 7, 7, 7, 7, 7];
let M_HOCKEY = [4, 5, 5, 5, 5, 4, 5, 4, 5, 8, 8, 5];

let WEEKS = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];

var chartData = {
  type: 'line',
  data: {
    labels: WEEKS,
    datasets: [{
      label: "W Basketball",
      backgroundColor: YELLOW,
      borderColor: YELLOW,
      data: W_BBALL,
      fill: false,
      lineTension: 0
    }, {
      label: "M Basketball",
      fill: false,
      backgroundColor: LIGHT_GREEN,
      borderColor: LIGHT_GREEN,
      data: M_BBALL,
      lineTension: 0
    }, {
      label: "W Hockey",
      fill: false,
      backgroundColor: LIGHT_BLUE,
      borderColor: LIGHT_BLUE,
      data: W_HOCKEY,
      lineTension: 0
    }, {
      label: "M Hockey",
      fill: false,
      backgroundColor: DARK_RED,
      borderColor: DARK_RED,
      data: M_HOCKEY,
      lineTension: 0
    }]
  },
  options: {
    responsive: true,
    title:{
      display:true,
    },
    tooltips: {
      // mode: 'index', // toggle between showing all labels or individual
      intersect: false,
    },
   hover: {
      mode: 'nearest',
      intersect: true
    },
    scales: {
      xAxes: [{
        display: true,
        scaleLabel: {
          display: true,
          labelString: 'Week'
        }
      }],
      yAxes: [{
        display: true,
        scaleLabel: {
          display: true,
          labelString: 'Ranking'
        },
      }]
    }
  }
};

var ctx = document.getElementById("league-position").getContext("2d");
var myLine = new Chart(ctx, chartData);
// var pieChart = new Chart(ctx, {
//   type: 'pie',
//   data: chartData
// })