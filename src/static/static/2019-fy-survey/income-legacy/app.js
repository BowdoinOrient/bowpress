var ctx = document.getElementById("incomeLegacy").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Below $40,000", "$40,000-$80,000",	"$80,000-$125,000",	"$125,000-$250,000",	"$250,000-$500,000",	"More than $500,000"],
    datasets: [{
      label: 'Not Legacy',
      backgroundColor: "#CAB1D1",
      data: [88.9,	92.6,	73.2,	81.5,	78.0,	69.0,],
    }, {
      label: 'Legacy',
      backgroundColor: "#8974A5",
      data: [11.1,	7.4,	26.8,	18.5,	22.0,	31.0,],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 100,
               callback: function(value){return value+ "%"}
            },  
            scaleLabel: {
               display: true,
               labelString: "Percentage"
            }
        }]
    }
}
});
