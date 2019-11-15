var ctx = document.getElementById("xc-women").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Bowdoin Inv. 1", "Maine St. Meet",	"New England Open", "Bowdoin Inv. 2",	"NESCAC Champ."],
    datasets: [{
      label: 'Women',
      backgroundColor: "#CAB1D1", 
      data: [24.02,19.42,20.30,23.33,23.25],
    }]
  },
  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 15,
               max: 26,
            },  
            scaleLabel: {
               display: true,
               labelString: "Time (Minutes)"
            }
        }]
    }
}
});