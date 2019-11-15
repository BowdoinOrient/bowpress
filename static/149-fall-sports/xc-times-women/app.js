var ctx = document.getElementById("xc-times-women").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Bowdoin Invitational 1", "Maine State Meet",	"New England Open",	"Bowdoin Invitational 2",	"NESCAC Championship"],
    datasets: [{
      label: 'Women',
      backgroundColor: "#CAB1D1", 
      data: [24.02,	19.42, 20.30,	23.33,	23.25],
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

