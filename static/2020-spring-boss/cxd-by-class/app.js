var ctx = document.getElementById("cxd-by-class").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

// Sr: 52.2% approval, 24.2% disapproval
// Jr: 51.3% approval, 16.5% disapproval
// Sop: 57.7% approval, 12.4% disapproval
// Fr: 57% approval, 6.3% disapproval
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Class of 2023", "Class of 2022", "Class of 2021", "Class of 2020"],
    datasets: [{
      label: 'Disapprove',
      backgroundColor: "#8974A5",
      stack: 'Stack 0',
      data: [-6.3, -12.4, -16.5, -24.2],
    }, {
      label: 'Approve',
      backgroundColor: "#48639C",
      stack: 'Stack 0',
      data: [57, 57.7, 51.3, 52.2],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: -30,
               max: 70,
            },  
        }]
    }
}
});
