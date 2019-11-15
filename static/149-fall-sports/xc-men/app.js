var ctx = document.getElementById("xc-men").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Bowdoin Inv. 1", "Maine St. Meet",	"New England Open",	"Bowdoin Inv. 2",	"NESCAC Champ."],
    datasets: [{
      label: 'Men',
      backgroundColor: "#CAB1D1", 
      data: [27.26,	27.30, 26.37,	27.7,	27.12],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 25,
               max: 28,
            },  
            scaleLabel: {
               display: true,
               labelString: "Time (Minutes)"
            }
        }]
    }
}
});
