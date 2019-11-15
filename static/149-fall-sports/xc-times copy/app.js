var ctx = document.getElementById("xc-times-men").getContext('2d');

var original = Chart.defaults.global.legend.onClick;
Chart.defaults.global.legend.onClick = function(e, legendItem) {
  update_caption(legendItem);
  original.call(this, e, legendItem);
};

var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Bowdoin Invitational 1", "Maine State Meet",	"New England Open",	"Bowdoin Invitational 2",	"NESCAC Championship",	"More than $500,000"],
    datasets: [{
      label: 'Men (8k)',
      backgroundColor: "#CAB1D1",
      data: [27.26,	27.30, 26.37,	27.7,	27.12],
    }, {
      label: 'Women (6k)',
      backgroundColor: "#8974A5",
      data: [24.02 , 19.42,	23.30, 23.33,	23.25,],
    }]
  },

  options: {
    scales: {
        yAxes: [{
        ticks: {
               min: 0,
               max: 30,
            },  
            scaleLabel: {
               display: true,
               labelString: "Time (Minutes)"
            }
        }]
    }
}
});
